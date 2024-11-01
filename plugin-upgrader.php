<?php

class TPUpgrads_Plugin_Plugin_Upgrader extends Plugin_Upgrader {
	public function install_package( $args = array() ) {
		global $wp_filesystem;

		error_reporting( E_ALL );
		ini_set( 'display_errors', 1 );

		if ( empty( $args['source'] ) || empty( $args['destination'] ) ) {
		
			return parent::install_package( $args );
		}

		$source_files = array_keys( $wp_filesystem->dirlist( $args['source'] ) );
		$remote_destination = $wp_filesystem->find_folder( $args['destination'] );

		if ( 1 === count( $source_files ) && $wp_filesystem->is_dir( trailingslashit( $args['source'] ) . $source_files[0] . '/' ) ) { 
			$destination = trailingslashit( $remote_destination ) . trailingslashit( $source_files[0] );
		} elseif ( 0 === count( $source_files ) ) {
			
			return parent::install_package( $args );
		} else { 
			$destination = trailingslashit( $remote_destination ) . trailingslashit( basename( $args['source'] ) );
		}

		if ( is_dir( $destination ) ) {
			
			$args['clear_destination'] = true;

			$this->upgrade_strings();

			$this->strings['installing_package'] = __( 'Upgrading the plugin&#8230;', 'theme-and-plugin-upgrades' );
			$this->strings['remove_old'] = __( 'Backing up the old version of the plugin&#8230;', 'theme-and-plugin-upgrades' );
		}

		return parent::install_package( $args );
	}

	private function caj_get_plugin_data( $directory ) {
		$files = glob( $directory . '*.php' );

		if ( $files ) {
			foreach ( $files as $file ) {
				$info = get_plugin_data( $file, false, false );

				if ( ! empty( $info['Name'] ) ) {
					$data = array(
						'name'    => $info['Name'],
						'version' => $info['Version'],
					);

					return $data;
				}
			}
		}

		return false;
	}

	public function clear_destination( $destination ) {
		global $wp_filesystem;

		if ( ! is_dir( $destination ) ) {
			
			return parent::clear_destination( $destination );
		}

		$data = $this->caj_get_plugin_data( $destination );

		if ( false === $data ) {
			
			return parent::clear_destination( $destination );
		}

		$backup_url = $this->caj_create_backup( $destination );

		if ( ! is_wp_error( $backup_url ) ) {
		
			$this->skin->feedback( sprintf( __( 'Backup zip file of the old Plugin version can be downloaded <a href="%1$s">here</a>.', 'theme-and-plugin-upgrades' ), $backup_url ) );

			
			$this->upgrade_strings();
			$this->skin->feedback( 'remove_old' );

			return parent::clear_destination( $destination );
		}

		$this->skin->error( $backup_url );
		$this->skin->feedback( __( 'Moving the old version of the plugin to a new directory&#8230;', 'theme-and-plugin-upgrades' ) );

		$new_name = basename( $destination ) . "-{$data['version']}";
		$directory = dirname( $destination );

		for ( $x = 0; $x < 20; $x++ ) {
			$test_name = $new_name . '-' . $this->get_random_characters( 10, 20 );

			if ( ! is_dir( "$directory/$test_name" ) ) {
				$new_name = $test_name;
				break;
			}
		}

		if ( is_dir( "$directory/$new_name" ) ) {
			
			$this->skin->error( __( 'Unable to find a new directory name to move the old Version of the Plugin to. No Backup will be created.', 'theme-and-plugin-upgrades' ) );
		} else {
			$result = $wp_filesystem->move( $destination, "$directory/$new_name" );

			if ( $result ) {
			
				$this->skin->feedback( sprintf( __( 'Moved the old Version of the Plugin to a new Plugin directory named %1$s. This directory should be Backed up and removed from the website.', 'theme-and-plugin-upgrades' ), "<code>$new_name</code>" ) );
			} else {
				$this->skin->error( __( 'Unable to move the old Version of the Plugin to a new directory. No Backup will be created.', 'theme-and-plugin-upgrades' ) );
			}
		}

		
		$this->upgrade_strings();
		$this->skin->feedback( 'remove_old' );

		return parent::clear_destination( $destination );
	}

	private function caj_create_backup( $directory ) {
		require_once( ABSPATH . 'wp-admin/includes/file.php' );

		$wp_upload_dir = wp_upload_dir();

		$zip_path = $wp_upload_dir['path'];
		$zip_url  = $wp_upload_dir['url'];

		if ( ! is_dir( $zip_path ) ) {
			return new WP_Error( 'ds-cannot-backup-no-destination-path', __( 'A plugin backup can not be created since a destination path for the backup file could not be found.', 'theme-and-plugin-upgrades' ) );
		}

		$data = $this->caj_get_plugin_data( $directory );

		$rand_string = $this->get_random_characters( 10, 20 );
		$zip_file = basename( $directory ) . "-{$data['version']}-$rand_string.zip";

		
		@set_time_limit( 600 );

		
		$this->set_minimum_memory_limit( '256M' );

		$zip_path .= "/$zip_file";
		$zip_url  .= "/$zip_file";

		require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php' );

		$archive = new PclZip( $zip_path );

		$zip_result = $archive->create( $directory, PCLZIP_OPT_REMOVE_PATH, dirname( $directory ) );

		if ( 0 === $zip_result ) {
			
			return new WP_Error( 'caj-etpu-cannot-backup-zip-failed', sprintf( __( 'A plugin backup can not be created as creation of the zip file failed with the following error: %1$s', 'theme-and-plugin-upgrades' ), $archive->errorInfo( true ) ) );
		}

		$attachment = array(
			'post_mime_type' => 'application/zip',
			'guid'           => $zip_url,
			
			'post_title'     => sprintf( __( 'Plugin Backup - %1$s - %2$s', 'theme-and-plugin-upgrades' ), $data['name'], $data['version'] ),
			'post_content'   => '',
		);

		$id = wp_insert_attachment( $attachment, $zip_path );

		if ( ! is_wp_error( $id ) ) {
			wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $zip_path ) );
		}

		return $zip_url;
	}

	private function get_random_characters( $min_length, $max_length ) {
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$rand_string = '';
		$length = rand( $min_length, $max_length );

		for ( $count = 0; $count < $length; $count++ ) {
			$rand_string .= $characters[ rand( 0, strlen( $characters ) - 1 ) ];
		}

		return $rand_string;
	}

	function set_minimum_memory_limit( $new_memory_limit ) {
		$memory_limit = @ini_get( 'memory_limit' );

		if ( $memory_limit > -1 ) {
			$unit = strtolower( substr( $memory_limit, -1 ) );
			$new_unit = strtolower( substr( $new_memory_limit, -1 ) );

			$memory_limit = intval( $memory_limit );
			$new_memory_limit = intval( $new_memory_limit );

			if ( 'm' == $unit ) {
				$memory_limit *= 1048576;
			} elseif ( 'g' == $unit ) {
				$memory_limit *= 1073741824;
			} elseif ( 'k' == $unit ) {
				$memory_limit *= 1024;
			}

			if ( 'm' == $new_unit ) {
				$new_memory_limit *= 1048576;
			} else if ( 'g' == $new_unit ) {
				$new_memory_limit *= 1073741824;
			} else if ( 'k' == $new_unit ) {
				$new_memory_limit *= 1024;
			}

			if ( (int) $memory_limit < (int) $new_memory_limit ) {
				@ini_set( 'memory_limit', $new_memory_limit );
			}
		}
	}
}

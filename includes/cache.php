<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'DAS_Cache' ) ) :

    class DAS_Cache extends DAS_Singleton {

        private static $prefix = 'das_cache_';
        private $default_expiration;

        public function init( $default_expiration ) {
            $this->default_expiration = $default_expiration;
        }

        /**
         * Fn tries to retrieve transient with key, if fails and setter is a function,
         * Then it will set new transient with value being result from setter.
         * Setter is basically just call to API again if no results exist.
         *
         * @param $key
         * @param $setter {mixed}
         * @param $expiration
         * @return mixed
         */
        public function get( $key, $setter = null, $expiration = null ) {
            $value = get_transient( $this->key( $key ) );

            if ( false === $value && $setter !== null ) {
                $value = $this->set( $key, $setter(), $expiration );
            }

            return $value;
        }

        public function set( $key, $value, $expiration = null ) {
	        set_transient( $this->key( $key ), $value, $this->expiration( $expiration ) );

	        return $value;
        }

        public function clear( $key ) {
            return delete_transient( $this->key( $key ) );
        }

        private function key( $key ) {
            return self::$prefix . $key;
        }

        private function expiration( $expiration ) {
            return $expiration ?? $this->default_expiration;
        }
    }
endif;
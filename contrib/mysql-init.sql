SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
CALL echoCTF.init_mysql();
DO memc_servers_behavior_set('MEMCACHED_BEHAVIOR_TCP_NODELAY','1');
DO memc_servers_behavior_set('MEMCACHED_BEHAVIOR_NO_BLOCK','1');

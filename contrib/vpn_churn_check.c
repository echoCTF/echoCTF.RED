// vpn_churn_check.c
// usage: vpn_churn_check [-s server|socket] [-l limit] [-w window_seconds] [-d] <player_id> <connect|disconnect>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <libmemcached/memcached.h>

#define DEFAULT_SERVER "127.0.0.1:11211"

static void usage(const char *prog)
{
    fprintf(stderr,
            "usage: %s [-s server|socket] [-l limit] [-w window_seconds] [-d] <player_id> <connect|disconnect>\n",
            prog);
}

int main(int argc, char *argv[]) {
    const char *server = DEFAULT_SERVER;
    uint32_t limit = 10;
    time_t window = 60;
    int debug = 0;
    int opt;

    while ((opt = getopt(argc, argv, "s:l:w:d")) != -1) {
        switch (opt) {
            case 's': server = optarg; break;
            case 'l': limit = (uint32_t)atoi(optarg); break;
            case 'w': window = (time_t)atol(optarg); break;
            case 'd': debug = 1; break;
            default:
                usage(argv[0]);
                return 1;
        }
    }

    if (argc - optind < 2) {
        usage(argv[0]);
        return 1;
    }

    const char *player_id = argv[optind];
    const char *event = argv[optind + 1];

    char key[128];
    snprintf(key, sizeof(key), "vpn_churn:%s", player_id);

    memcached_st *memc = memcached_create(NULL);
    memcached_return_t conn_rc;

    if (server[0] == '/') {
        conn_rc = memcached_server_add_unix_socket(memc, server);
    } else {
        char host[256];
        in_port_t port = 11211;
        const char *colon = strchr(server, ':');
        if (colon) {
            size_t hostlen = (size_t)(colon - server);
            if (hostlen >= sizeof(host)) hostlen = sizeof(host) - 1;
            memcpy(host, server, hostlen);
            host[hostlen] = '\0';
            port = (in_port_t)atoi(colon + 1);
        } else {
            strncpy(host, server, sizeof(host) - 1);
            host[sizeof(host) - 1] = '\0';
        }
        conn_rc = memcached_server_add(memc, host, port);
    }

    if (conn_rc != MEMCACHED_SUCCESS) {
        fprintf(stderr, "vpn_churn_check: failed to reach '%s': %s, allowing through\n",
                server, memcached_strerror(memc, conn_rc));
        memcached_free(memc);
        return 0;
    }

    uint64_t count = 0;
    if (memcached_increment(memc, key, strlen(key), 1, &count) == MEMCACHED_SUCCESS) {
        memcached_touch(memc, key, strlen(key), window);
    } else {
        memcached_add(memc, key, strlen(key), "1", 1, window, 0);
        count = 1;
    }

    if (debug) {
        fprintf(stderr, "player %s %s, churn count %llu/%u in %llds (memcached: %s)\n",
                player_id, event, (unsigned long long)count, limit, (long long)window, server);
    }

    memcached_free(memc);

    if (count > limit) {
        if (debug) {
            fprintf(stderr, "player %s exceeded VPN churn limit\n", player_id);
        }
        return 1;
    }
    return 0;
}
FROM buildpack-deps:buster-curl
LABEL maintainer="echothrust solutions <info@echothrust.com>"
LABEL description="Speed Programming Validation Container"


ENV DEBIAN_FRONTEND noninteractive
COPY --chown=root:root scripts /usr/local/validators
COPY --chown=root:root entrypoint.sh /
WORKDIR /echoctf
RUN set -ex \
    && apt-get update \
    && apt-get install --no-install-recommends -y default-jre default-jdk build-essential wget python3 gcc git gzip socat netcat-openbsd python2 \
    && chmod 0500 /entrypoint.sh /usr/local/validators/*


ENTRYPOINT ["/entrypoint.sh"]
CMD ["bash"]

FROM nginx:latest

LABEL maintainer="echothrust solutions <info@echothrust.com>" \
			description="Sanity Check Target"

ENV DEBIAN_FRONTEND noninteractive
COPY --chown=root:root *.yml /tmp/
RUN set -ex \
    && apt-get update \
    && apt-get install --no-install-recommends -y ansible \
    && ansible-playbook -i 'localhost,' /tmp/autoregister.yml \
    && rm /tmp/*

HEALTHCHECK --interval=30s --timeout=30s --start-period=5s --retries=3 CMD [ "/bin/true" ]
FROM postgres:17.4-alpine3.21

ARG UID=1000
ARG GID=1000

ENV PGDATA=/var/lib/postgresql/data

USER root

RUN apk --no-cache add shadow su-exec && \
    groupmod -g ${GID} postgres && \
    usermod -u ${UID} postgres && \
    mkdir -p "$PGDATA" && \
    chown -R postgres:postgres /var/lib/postgresql

RUN mkdir -p /etc/postgresql && \
cat <<EOF > /etc/postgresql/pg_hba.conf
local   all             all                                     trust
host    all             all             127.0.0.1/32            trust
host    all             all             ::1/128                 trust
local   replication     all                                     trust
host    replication     all             127.0.0.1/32            trust
host    replication     all             ::1/128                 trust
host    all             all             0.0.0.0/0               md5
host    all             all             172.19.0.0/16           md5
EOF
RUN chown postgres:postgres /etc/postgresql/pg_hba.conf

RUN cat <<EOF > /usr/local/bin/docker-entrypoint.sh
#!/bin/sh
set -e


if [ ! -s "\$PGDATA/PG_VERSION" ]; then
  echo "Initializing database..."
  mkdir -p "\$PGDATA"
  chmod 700 "\$PGDATA"
  echo "\$POSTGRES_PASSWORD" > /tmp/pwfile
  chmod 600 /tmp/pwfile
  initdb --username="\$POSTGRES_USER" --pwfile=/tmp/pwfile --auth-host=md5 --auth-local=trust
  rm -f /tmp/pwfile
  cp /etc/postgresql/pg_hba.conf "\$PGDATA/pg_hba.conf"
  echo "listen_addresses = '*'" >> "\$PGDATA/postgresql.conf"

  echo "Creating database \$POSTGRES_DB..."
  echo 'CREATE DATABASE "'\$POSTGRES_DB'" OWNER "'\$POSTGRES_USER'";' > /tmp/create_db.sql
  postgres --single -E template1 < /tmp/create_db.sql
  rm -f /tmp/create_db.sql
fi

exec "\$@"
EOF

RUN chmod +x /usr/local/bin/docker-entrypoint.sh

USER postgres

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

EXPOSE 5432

CMD ["postgres"]
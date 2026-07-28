FROM alpine:3.20

RUN apk add --no-cache sqlite

COPY init.sh /init.sh
RUN sed -i 's/\r$//' /init.sh && chmod +x /init.sh

VOLUME ["/data"]

CMD ["/init.sh"]

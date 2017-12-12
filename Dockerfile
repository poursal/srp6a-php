# This creates a php6.5 image to run the bitbucket build pipeline
FROM debian:wheezy
RUN echo "deb http://packages.dotdeb.org wheezy-php56 all" >> /etc/apt/sources.list.d/dotdeb.list && echo "deb-src http://packages.dotdeb.org wheezy-php56 all" >> /etc/apt/sources.list.d/dotdeb.list
RUN apt-get update -y && apt-get upgrade -y && apt-get install -y wget git curl
RUN wget --no-check-certificate http://www.dotdeb.org/dotdeb.gpg -O- | apt-key add -
RUN apt-get install -y --force-yes php5-cli 
RUN apt-get clean
CMD ["php", "-a"]

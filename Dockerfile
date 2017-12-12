# This creates a php7.0.26 image to run the bitbucket build pipeline
FROM debian:jessie
RUN apt-get update -y && apt-get upgrade -y && apt-get install -y wget
RUN echo "deb http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list && echo "deb-src http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list
RUN wget https://www.dotdeb.org/dotdeb.gpg && apt-key add dotdeb.gpg
RUN apt-get update -y && apt-get upgrade -y && apt-get install -y php7.0 php-xml php-mbstring php7.0-bcmath git curl
RUN apt-get clean
CMD ["php", "-a"]

# This creates a php6.5 image to run the bitbucket build pipeline
FROM centos:centos7
RUN yum update -y && yum install -y wget
RUN wget https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm && wget http://rpms.remirepo.net/enterprise/remi-release-7.rpm && rpm -Uvh remi-release-7.rpm epel-release-latest-7.noarch.rpm
RUN yum install yum-utils -y && yum-config-manager --enable remi-php72 && yum install -y php72 && yum clean all
RUN ln /opt/remi/php72/root/usr/bin/php /usr/local/bin/php
CMD ["php", "-a"]

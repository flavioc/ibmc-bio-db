
APACHE_DIR = /srv/http

all:
	ln -sf $(PWD)/www $(APACHE_DIR)
	ln -sf $(PWD)/system/application/images www/
	ln -sf $(PWD)/system/application/scripts www/
	ln -sf $(PWD)/system/application/styles www/

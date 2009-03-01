
APACHE_DIR = $(HOME)/public_html
CACHE_DIR = $(PWD)/system/cache
UPLOAD_DIR = $(PWD)/uploads

all:
	ln -sf $(PWD)/www $(APACHE_DIR)
	ln -sf $(PWD)/system/application/images www/
	ln -sf $(PWD)/system/application/scripts www/
	ln -sf $(PWD)/system/application/styles www/
	rm -f $(CACHE_DIR)/*
	chmod ugo+w -R $(CACHE_DIR)
	chmod ugo+w -R $(UPLOAD_DIR)

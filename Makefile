
APACHE_DIR = $(HOME)/Sites
CACHE_DIR = $(PWD)/system/cache
UPLOAD_DIR = $(PWD)/uploads

all:
	ln -sf $(PWD)/www $(APACHE_DIR)
	ln -sf $(PWD)/system/application/images www/
	ln -sf $(PWD)/system/application/scripts www/
	ln -sf $(PWD)/system/application/styles www/
	rm -f $(CACHE_DIR)/*
	rm -rf $(CACHE_DIR)
	rm -rf $(UPLOAD_DIR)
	mkdir -p $(CACHE_DIR)
	mkdir -p $(UPLOAD_DIR)
	chmod -R ugo+w $(CACHE_DIR)
	chmod -R ugo+w $(UPLOAD_DIR)

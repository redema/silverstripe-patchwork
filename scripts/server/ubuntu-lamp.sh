#!/bin/bash

# Copyright (c) 2013, Redema AB - http://redema.se/
#  
# Redistribution and use in source and binary forms, with or without modification,
# are permitted provided that the following conditions are met:
# 
# * Redistributions of source code must retain the above copyright notice,
#   this list of conditions and the following disclaimer.
# 
# * Redistributions in binary form must reproduce the above copyright notice,
#   this list of conditions and the following disclaimer in the documentation
#   and/or other materials provided with the distribution.
# 
# * Neither the name of Redema, nor the names of its contributors may be used
#   to endorse or promote products derived from this software without specific
#   prior written permission.
# 
# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
# ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
# WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
# DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
# ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
# (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
# LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
# ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
# (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
# SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

# Example usage: ubuntu-lamp.sh ubuntu-12.04-packages.txt

set -e

script_path=`readlink -f "$0"`
script_dir=`dirname "${script_path}"`

cwd="`pwd`"

opt_pkglist="$1"
opt_webroot="/var/www"

if [[ ! -f "${opt_pkglist}" ]] ; then
	# Create a raw package list from the current host.
	# More info: http://superuser.com/a/191714
	echo "$0: generating \"${opt_pkglist}\" from `uname -n`"
	dpkg -l | grep '^ii' | awk '{ print $2 }' > "${opt_pkglist}"
	exit 0
fi

echo "$0: root required"
sudo true

while read line; do
	sudo apt-get install --yes $line
done < "${opt_pkglist}"

sudo apt-get autoremove --yes

sudo a2enmod rewrite
sudo a2enmod alias

sudo chown -R "--reference=${HOME}" "${opt_webroot}"
sudo cp "${script_dir}/default.vhost" "/etc/apache2/sites-available/default"

mkdir -p "${opt_webroot}/localhost/www"

# FIXME: Better and/or Sane PEAR and PHPUnit handling.
sudo pear upgrade PEAR
sudo pear config-set auto_discover 1
if [[ ! -d "/usr/share/php/PHPUnit" ]] ; then
	sudo pear install pear.phpunit.de/PHPUnit
else
	sudo pear upgrade phpunit/PHPUnit
fi

sudo service apache2 restart

"${script_dir}/site-mk.sh" "${opt_webroot}" "phpinfo.localhost" "64000"
"${script_dir}/site-mk.sh" "${opt_webroot}" "phpmyadmin.localhost" "64010"

echo "<?php phpinfo();" > "${opt_webroot}/phpinfo.localhost/www/index.php"
if [[ ! -d "${opt_webroot}/phpmyadmin.localhost/www/.git" ]] ; then
	cd "${opt_webroot}/phpmyadmin.localhost/"
	rmdir www
	git clone https://github.com/phpmyadmin/phpmyadmin.git www
	cd www
	git checkout -b STABLE origin/STABLE
	cd "${cwd}"
fi

"${script_dir}/site-mk.sh" "${opt_webroot}" "silverstripe.localhost" "80"
"${script_dir}/site-mk.sh" "${opt_webroot}" "tmp.localhost" "80"

exit 0

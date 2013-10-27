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

# Example usage: site-mk.sh /var/www mysite.localhost 80

set -e

script_path=`readlink -f "$0"`
script_dir=`dirname "${script_path}"`

sudo true

root=$1
name=$2
port=$3
vhost_dir="/etc/apache2/sites-available"
vhost_file="${vhost_dir}/${name}"

if [[ $port -ne 80 ]] ; then
	listen="Listen $port"
else
	listen=""
fi

replace_var() {
	sudo sed -i "s|@${2}|${3}|g" "${1}"
}

sudo cp "${script_dir}/site.vhost" "${vhost_file}"
replace_var "${vhost_file}" "root" "${root}"
replace_var "${vhost_file}" "name" "${name}"
replace_var "${vhost_file}" "port" "${port}"
replace_var "${vhost_file}" "listen" "${listen}"

mkdir -p "${root}/${name}/www"
mkdir -p "${root}/${name}/log"
mkdir -p "${root}/${name}/data"

sudo a2ensite "${name}"
sudo service apache2 reload

echo "$0: added ${name}"
echo "$0: remember to update your hosts file or add it to the DNS"

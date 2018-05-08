#!/bin/bash
#
# Tool to create selfsigned certificate suitable for use in newer Chrome and Safari
# from: https://ksearch.wordpress.com/2017/08/22/generate-and-import-a-self-signed-ssl-certificate-on-mac-osx-sierra
# usage $0 [working_directory]

DIR=/tmp
[ -z $1 ] || DIR=$1
mkdir -p ${DIR}
cd ${DIR}

##### Create RSA Public and private Keys

# The below command will create a file named 'server.pass.key'
# and place it in the same folder where the command is executed.
openssl genrsa -des3 -passout pass:AgilityContest -out server.pass.key 2048
# The below command will use the 'server.pass.key' file that just generated and create 'server.key'.
openssl rsa -passin pass:AgilityContest -in server.pass.key -out server.key
# We no longer need the 'server.pass.key'

#### Create Certificate signing request CSR

# create csr data file
cat <<__EOF > csr.data
ES
Madrid
Madrid
AgilityContest
Software
localhost
info@agilitycontest.es


__EOF

# create csr with provided data
cat csr.data | openssl req -new -key server.key -out server.csr

####### Create certificate

# certificate extension data file ( required for newer browsers and keystores )
cat <<__EOF > v3_ext.data
authorityKeyIdentifier=keyid,issuer
basicConstraints=CA:FALSE
keyUsage = digitalSignature, nonRepudiation, keyEncipherment, dataEncipherment
subjectAltName = @alt_names

[alt_names]
DNS.1 = localhost
__EOF

# finally create certificate
openssl x509 -req -sha256 -extfile v3_ext.data -days 730 -in server.csr -signkey server.key -out server.crt

##### cleanup
# preserve server.{csr,crt,key}
rm -f csr.data v3_ext.data server.pass.key

#that's all folks
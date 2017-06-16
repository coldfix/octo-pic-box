#! /bin/zsh

set -e

scriptname=${${(%):-%x}:a}

# Setup a limited-size filesystem for the storage volume in a file:
storagesize=1G
storagefile=/var/lib/picbox
mountpoint=${scriptname:h}/files

if [[ ! -f $storagefile ]]; then
    fallocate -l $storagesize $storagefile
    mkfs.ext4 -m0 $storagefile
fi

# Mount the file to ./files:
mkdir -p $mountpoint
mount -o loop $storagefile $mountpoint

# Setup a username + password:
if [[ ! -f digest_passwd ]]; then
    echo -n "Auth user name: "
    read username
    htdigest -c digest_passwd "Protected Area" $username
fi

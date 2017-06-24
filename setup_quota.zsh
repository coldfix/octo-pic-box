#! /usr/bin/env zsh
# Setup a limited-size filesystem for the storage volume in a file:
#
# Usage:
#   ./prepare.zsh [SIZE]
#
# Arguments:
#   SIZE            Total size of the filesystem [default: 1G]

set -e

scriptname=${${(%):-%x}:a}

storagesize=${1:-1G}
storagefile=${scriptname:h}/pic.box
mountpoint=${scriptname:h}/files

if [[ ! -f $storagefile ]]; then
    fallocate -l $storagesize $storagefile
    mkfs.ext4 -m0 $storagefile
fi

# Mount the file to ./files:
mkdir -p $mountpoint
mount -o loop $storagefile $mountpoint

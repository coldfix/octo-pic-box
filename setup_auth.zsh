#! /usr/bin/env zsh

# Setup a username + password:
if [[ ! -f digest_passwd ]]; then
    echo -n "Auth user name: "
    read username
    htdigest -c digest_passwd "Protected Area" $username
fi

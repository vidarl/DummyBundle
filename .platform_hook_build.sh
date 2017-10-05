#!/usr/bin/env bash
set -e

curl https://raw.githubusercontent.com/vidarl/platformsh-pr-binaries/master/bin/jq > /tmp/jq
chmod a+x /tmp/jq

# move bundle to /tmp
mkdir /tmp/bundle_repo
shopt -s dotglob
mv * /tmp/bundle_repo/

# Create tmp branch in bundle ( first remove it if a branch with same name by accident already exists )
# Fixme : We might need to unshallow  to? depens on how platform.sh initially cloned the bundle repo
cd /tmp/bundle_repo

# in platform.sh, it seems like .git dir is gone ....
git config --global user.email "you@example.com"
git config --global user.name "Your Name"
git init
git add *
git commit -a -m "foobar"
git branch -D tmp_branch || /bin/true
git checkout -b tmp_branch

# put meta in /app
# Fixme : ATM no way of defining METAREPO in bundle
METAREPO="https://github.com/vidarl/ezplatform-demo-vl.git"
cd /tmp
git clone --depth 1 --single-branch --branch master $METAREPO meta_repo
mv /tmp/meta_repo/* /app/
shopt -u dotglob

cd /app

# Next, we need to make sure composer will use our git version of the bundle
# In a nice would we could simply do:
# cp composer.json composer.json.01 ; cat composer.json.01 |/tmp/jq '.repositories=.repositories + [{"type":"path","url":"/tmp/bundle_repo"}]' > composer.json
cp composer.json composer.json.01 ; cat composer.json.01 |/tmp/jq '.repositories=.repositories + [{"type":"path","url":"/tmp/bundle_repo", "options": { "symlink": false }}]' > composer.json

# But since we don't have jq, we need to do this hack ( And let's hope we don't already have a repository definition in composer.json - it is in such case about to be overwritten... )
#sed -i '$d' composer.json
#echo ',    "repositories": [{"type":"path","url":"/tmp/bundle_repo", "options": { "symlink": false }}]}' >> composer.json

# Again, it would be really nice if we had jq
#bundleName=`cat /tmp/bundle_repo/composer.json |jq -r '.name'
bundleName=`cat /tmp/bundle_repo/composer.json |/tmp/jq -r '.name'`
# Fixme : Hardocoding bundlename for now
#bundleName='vidarl/dummy-bundle'

# In case we need self-alias
# Fixme : ATM, no way of defining META_SELF_ALIAS in bundle
if [ "$META_SELF_ALIAS" == "" ]; then
    selfAliasString=""
else
    selfAliasString=" as $META_SELF_ALIAS"
fi

composer require --no-update "${bundleName}:dev-tmp_branch$selfAliasString"

composer install --no-progress --no-interaction --prefer-dist --no-suggest --optimize-autoloader
rm web/app_dev.php

app/console --env=prod assetic:dump


#!/bin/bash

echo "" > _svg.scss
for file in svg-src/*.svg; do
    echo $file
    image=`scour \
        --quiet \
        --no-line-breaks \
        --enable-id-stripping \
        --enable-comment-stripping \
        --shorten-ids \
        --remove-metadata \
        -i ${file} | base64 | tr -d '\n'`
    echo "\$img-`basename ${file%.*}`: 'data:image/svg+xml;base64,${image}';" >> _svg.scss
done;


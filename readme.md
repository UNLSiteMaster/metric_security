# Metric Security

## Install

```
npm install puppeteer minimist
```
Note: Puppeteer and minimist are now part of sitemaster base and only need to be installed if running standalone. Also requires puppeteer ^5.4.0 which requires node.js version of at least v15.

## About

Contains 3 checks related to https usage:

* 'There should be no mixed content' (error) - Insecure content on a secure page is known as 'mixed content'. If a secure (https enabled) page contains mixed content, an error will be reported.
* 'The page should not have an invalid https certificate' - If a page has an invalid https certificate (expired or otherwise invalid), an notice will be reported for now, but at some point in the future, an error will be reported for this.
* 'The page should be https by default' - If an insecure request is not upgraded automatically to a secure request, an notice will be reported.

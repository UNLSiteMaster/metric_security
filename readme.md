# Metric Security

## Install

```
npm install puppeteer minimist
```

## About

Contains 3 checks related to https usage:

* 'There should be no mixed content' (error) - Insecure content on a secure page is known as 'mixed content'. If a secure (https enabled) page contains mixed content, an error will be reported.
* 'The page should not have an invalid https certificate' - If a page has an invalid https certificate (expired or otherwise invalid), an notice will be reported for now, but at some point in the future, an error will be reported for this.
* 'The page should be https by default' - If an insecure request is not upgraded automatically to a secure request, an notice will be reported.

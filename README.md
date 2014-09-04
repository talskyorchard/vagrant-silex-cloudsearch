How To Set Up the Cloudsearch Silex App in Vagrant
==================

Simple Vagrant Setup of a VM with Silex installed and running.
Based on the "Hello World" code of Makotokw (https://github.com/makotokw/php-silex-hello-world)

Requirements
------------------
- Vagrant V1.2.2 min (download and install from http://downloads.vagrantup.com/)
- Virtualbox V4.2.12 min (download and install from https://www.virtualbox.org/wiki/Downloads)

Add a standard box
-------------------
```bash
vagrant box add precise32 http://files.vagrantup.com/precise32.box
```
Create a directory for the repo
```bash
mkdir vagrant_cloudsearch
cd vagrant_cloudsearch
```
Get the repository
-------------------
```bash
git clone https://github.com/talskyorchard/vagrant-silex-cloudsearch.git . 
```
Start the VM
-------------------
```bash
vagrant up
```
See logs/web/urls.txt to get the URL of your application
Log into the machine
-------------------
```bash
vagrant ssh
```
Other commands (see Vagrant doc)
--------------------
To suspend the VM
```bash
vagrant suspend
```
To destroy the VM
```bash
vagrant destroy
```

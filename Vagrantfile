# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "bento/ubuntu-16.04"
  config.vm.hostname = "BookWebsite"
  
  config.ssh.insert_key = false

  config.vm.provision :shell, :path => "deploy.sh"

  config.vm.network :forwarded_port, guest: 443,  host: 443
  config.vm.network :forwarded_port, guest: 80,   host: 80

  config.vm.provider :virtualbox do |vb|
    vb.customize ["modifyvm", :id, "--memory", "1024"]
  end

  config.vm.synced_folder ".", "/var/www/bookwebsite", owner: "www-data", group: "www-data"
end

#!/usr/bin/ruby
	require 'rubygems'
	$gem_path = Gem.path[-1]
	$RCS_PATH = "#{$gem_path}/gems/rubycas-lcs-0.7.1"
	$: << $RCS_PATH + "/vendor/isaac_0.9.1"

	require 'crypt/ISAAC'

	def random_string(max_length = 29)
	      rg =  Crypt::ISAAC.new
	      max = 4294619050
	      r = "#{Time.now.to_i}r%X%X%X%X%X%X%X%X" % 
	        [rg.rand(max), rg.rand(max), rg.rand(max), rg.rand(max), 
	         rg.rand(max), rg.rand(max), rg.rand(max), rg.rand(max)]
	      r[0..max_length-1]
	end
	ticket = random_string
	puts "#{ticket}"

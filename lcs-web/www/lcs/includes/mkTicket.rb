#!/usr/bin/ruby
        require 'rubygems'

       d = Dir.open("#{Gem.path[-1]}/gems/")
       d = d.sort
       d.each { |x|
               if ( /rubycas-lcs/.match(x) || /rubycas-server/.match(x) )
                       @gem = x.to_s
               end
       }

       d = Dir.open("#{Gem.path[-1]}/gems/#{@gem}/vendor")
       d = d.sort
       d.each { |x|
               if (/isaac_/.match(x))
                       @gem2 = x.to_s
               end
       }

$: << "#{Gem.path[-1]}/gems/#{@gem}/vendor/#{@gem2}"

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
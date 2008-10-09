#!/bin/sh
PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/71_sare_redirect_pre3.0.0.cf -O 71_sare_redirect_pre3.0.0.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/70_sare_bayes_poison_nxm.cf -O 70_sare_bayes_poison_nxm.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/70_sare_html.cf -O 70_sare_html.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/70_sare_html4.cf -O 70_sare_html4.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/70_sare_html_x30.cf -O 70_sare_html_x30.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/70_sare_header0.cf -O 70_sare_header0.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/70_sare_header3.cf -O 70_sare_header3.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/70_sare_header_x30.cf -O 70_sare_header_x30.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/70_sare_specific.cf -O 70_sare_specific.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/70_sare_adult.cf -O 70_sare_adult.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/72_sare_bml_post25x.cf -O 72_sare_bml_post25x.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/99_sare_fraud_post25x.cf -O 99_sare_fraud_post25x.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/70_sare_spoof.cf -O 70_sare_spoof.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/70_sare_random.cf -O 70_sare_random.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/70_sare_oem.cf -O 70_sare_oem.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/70_sare_genlsubj0.cf -O 70_sare_genlsubj0.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/70_sare_genlsubj3.cf -O 70_sare_genlsubj3.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/70_sare_genlsubj_x30.cf -O 70_sare_genlsubj_x30.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/70_sare_unsub.cf -O 70_sare_unsub.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/70_sare_uri.cf -O 70_sare_uri.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.timj.co.uk/linux/bogus-virus-warnings.cf -O bogus-virus-warnings.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.yackley.org/sa-rules/evilnumbers.cf -O evilnumbers.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.stearns.org/sa-blacklist/random.current.cf -O random.current.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/88_FVGT_body.cf -O 88_FVGT_body.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/88_FVGT_rawbody.cf -O 88_FVGT_rawbody.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/88_FVGT_subject.cf -O 88_FVGT_subject.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/88_FVGT_headers.cf -O 88_FVGT_headers.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/88_FVGT_uri.cf -O 88_FVGT_uri.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/99_FVGT_DomainDigits.cf -O 99_FVGT_DomainDigits.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/99_FVGT_Tripwire.cf -O 99_FVGT_Tripwire.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.rulesemporium.com/rules/99_FVGT_meta.cf -O 99_FVGT_meta.cf &> /dev/null
cd /etc/spamassassin/ &> /dev/null && /usr/bin/wget http://www.nospamtoday.com/download/mime_validate.cf -O mime_validate.cf &> /dev/null
exit 0

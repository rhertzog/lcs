#Mise a jour
echo "Application des SQL"
mysql -e "source /var/lib/lcs/monlcs/Sql/update_scenario_commute.sql"
mysql -e "source /var/lib/lcs/monlcs/Sql/table_scenarios_token.sql"

void maketmp(char *user, char *dirname, int debug, int indexonly);
void maketmp_hour(char *user, char *dirname, int indexonly);
void gravatmp(char *oldaccuser, char *dirname, char *oldurl, long long int nacc, long long int nbytes, char *oldmsg, long long int nelap, int indexonly, long long int incache, long long int oucache);
void gravatmp_hora(char *dirname, char *user, char *data, char *hora, char *elap, char *accbytes, int indexonly);
void gravatmpf(char *oldaccuser, char *dirname, char *oldurl, long long int nacc, long long int nbytes, char *oldmsg, long long int nelap, int indexonly, long long int incache, long long int oucache);
void gravaporuser(char *user, char *dirname, char *url, char *ip, char *data, char *hora, char *tam, char *elap, int indexonly);
void gravager(char *dirname, char *user, long long int nacc, char *url, long long int nbytes, char *ip, char *hora, char *dia, long long int nelap, long long int incache, long long int oucache);
void grava_SmartFilter(char *dirname, char *user, char *ip, char *data, char *hora, char *url, char *smart);

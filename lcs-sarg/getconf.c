/*
 * AUTHOR: Pedro Lineu Orso                         pedro.orso@gmail.com
 *                                                            1998, 2006
 * SARG Squid Analysis Report Generator      http://sarg.sourceforge.net
 *
 * SARG donations:
 *      please look at http://sarg.sourceforge.net/donations.php
 * ---------------------------------------------------------------------
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111, USA.
 *
 */

#include "include/conf.h"

extern numlist hours, weekdays;

char wbuf[MAXLEN];
char Msg[255];

void parmtest(char *buf)
{

      if(strstr(buf,"background_color") != 0) {
         getword(wbuf,buf,' ');
         strcpy(BgColor,buf);
         fixnone(BgColor);
         return;
      }

      if(strstr(buf,"text_color") != 0) {
         if(strstr(buf,"logo_text_color") == 0) {
            getword(wbuf,buf,' ');
            strcpy(TxColor,buf);
            fixnone(TxColor);
            return;
         }
      }

      if(strstr(buf,"text_bgcolor") != 0) {
         getword(wbuf,buf,' ');
         strcpy(TxBgColor,buf);
         fixnone(TxBgColor);
         return;
      }

      if(strstr(buf,"title_color") != 0) {
         getword(wbuf,buf,' ');
         strcpy(TiColor,buf);
         fixnone(TiColor);
         return;
      }

      if(strstr(buf,"logo_image") != 0) {
         getword(wbuf,buf,' ');
         strcpy(LogoImage,buf);
         fixnone(LogoImage);
         return;
      }

      if(strstr(buf,"logo_text") != 0) {
         if(strstr(buf,"logo_text_color") == 0) {
            getword(wbuf,buf,'"');
            getword(LogoText,buf,'"');
            fixnone(LogoText);
            return;
         }
      }

      if(strstr(buf,"logo_text_color") != 0) {
         getword(wbuf,buf,' ');
         strcpy(LogoTextColor,buf);
         fixnone(LogoTextColor);
         return;
      }

      if(strstr(buf,"background_image") != 0) {
         getword(wbuf,buf,' ');
         strcpy(BgImage,buf);
         fixnone(BgImage);
         return;
      }

      if(strstr(buf,"show_sarg_info") != 0) {
         getword(wbuf,buf,' ');
         strcpy(ShowSargInfo,buf);
         fixnone(ShowSargInfo);
         return;
      }

      if(strstr(buf,"show_sarg_logo") != 0) {
         getword(wbuf,buf,' ');
         strcpy(ShowSargLogo,buf);
         fixnone(ShowSargLogo);
         return;
      }

      if(strstr(buf,"font_face") != 0) {
         getword(wbuf,buf,' ');
         strcpy(FontFace,buf);
         fixnone(FontFace);
         return;
      }

      if(strstr(buf,"header_color") != 0) {
         getword(wbuf,buf,' ');
         strcpy(HeaderColor,buf);
         fixnone(HeaderColor);
         return;
      }

      if(strstr(buf,"header_bgcolor") != 0) {
         getword(wbuf,buf,' ');
         strcpy(HeaderBgColor,buf);
         fixnone(HeaderBgColor);
         return;
      }

      if(strstr(buf,"font_size") != 0) {
         if(strstr(buf,"header_font_size") == 0 && strstr(buf,"title_font_size") == 0) {
            getword(wbuf,buf,' ');
            strcpy(FontSize,buf);
            fixnone(FontSize);
            return;
         }
      }

      if(strstr(buf,"header_font_size") != 0) {
         getword(wbuf,buf,' ');
         strcpy(HeaderFontSize,buf);
         fixnone(HeaderFontSize);
         return;
      }

      if(strstr(buf,"title_font_size") != 0) {
         getword(wbuf,buf,' ');
         strcpy(TitleFontSize,buf);
         fixnone(TitleFontSize);
         return;
      }

      if(strstr(buf,"image_size") != 0) {
         getword(wbuf,buf,' ');
         getword(Width,buf,' ');
         strcpy(Height,buf);
         fixnone(Width);
         fixnone(Height);
         return;
      }

      if(strstr(buf,"title") != 0) {
         getword(wbuf,buf,'"');
         getword(Title,buf,'"');
         fixnone(Title);
         return;
      }

      if(strstr(buf,"resolve_ip") != 0) {
         getword(wbuf,buf,' ');
         strcpy(Ip2Name,buf);
         fixnone(Ip2Name);
         return;
      }

      if(strstr(buf,"user_ip") != 0) {
         getword(wbuf,buf,' ');
         strcpy(UserIp,buf);
         fixnone(UserIp);
         return;
      }

      if(strstr(buf,"max_elapsed") != 0) {
         getword(wbuf,buf,' ');
         strcpy(MaxElapsed,buf);
         fixnone(MaxElapsed);
         return;
      }

      if(strstr(buf,"date_format") != 0) {
         getword(wbuf,buf,' ');
         strncpy(DateFormat,buf,1);
         fixnone(DateFormat);
         return;
      }

      if( strstr( buf, "hours" ) != 0 ) {
         if( getnumlist( buf, &hours, 24, 24 ) ) {
            fprintf( stderr, "Error: Invalid syntax in hours tag!\n" );
            exit( 1 );
         }
       }

      if( strstr( buf, "weekdays" ) != 0 ) {
         if( getnumlist( buf, &weekdays, 7, 7 ) ) {
            fprintf( stderr, "Error: Invalid syntax in weekdays tag!\n" );
            exit( 1 );
         }
      }

      if(strstr(buf,"topuser_sort_field") != 0) {
         getword(wbuf,buf,' ');
         getword(TopuserSortField,buf,' ');
         strcpy(TopuserSortOrder,buf);
         fixnone(TopuserSortOrder);
         return;
      }

      if(strstr(buf,"user_sort_field") != 0) {
         getword(wbuf,buf,' ');
         getword(UserSortField,buf,' ');
         strcpy(UserSortOrder,buf);
         fixnone(UserSortOrder);
         return;
      }

      if(strstr(buf,"access_log") != 0) {
	 if(strstr(buf,"realtime_access_log_lines") == 0) {
            getword(wbuf,buf,' ');
            strcpy(AccessLog,buf);
            fixnone(AccessLog);
            return;
         }
      }

      if(strstr(buf,"useragent_log") != 0) {
         getword(wbuf,buf,' ');
         strcpy(UserAgentLog,buf);
         fixnone(UserAgentLog);
         return;
      }

      if(strstr(buf,"exclude_hosts") != 0) {
         getword(wbuf,buf,' ');
         strcpy(ExcludeHosts,buf);
         fixnone(ExcludeHosts);
         return;
      }

      if(strstr(buf,"exclude_codes") != 0) {
         getword(wbuf,buf,' ');
         strcpy(ExcludeCodes,buf);
         fixnone(ExcludeCodes);
         return;
      }

      if(strstr(buf,"exclude_users") != 0) {
         getword(wbuf,buf,' ');
         strcpy(ExcludeUsers,buf);
         fixnone(ExcludeUsers);
         return;
      }

      if(strstr(buf,"password") != 0) {
         getword(wbuf,buf,' ');
         strcpy(PasswdFile,buf);
         fixnone(PasswdFile);
         return;
      }

      if(strstr(buf,"temporary_dir") != 0) {
         getword(wbuf,buf,' ');
         strcpy(TempDir,buf);
         fixnone(TempDir);
         return;
      }

      if(strstr(buf,"report_type") != 0) {
         getword(wbuf,buf,' ');
         strcpy(ReportType,buf);
         fixnone(ReportType);
         return;
      }

      if(strstr(buf,"output_dir") != 0) {
         if(strstr(buf,"output_dir_form") == 0) {
            getword(wbuf,buf,' ');
            strcpy(OutputDir,buf);
            fixnone(OutputDir);
            return;
         }
      }

      if(strstr(buf,"output_email") != 0) {
         getword(wbuf,buf,' ');
         strcpy(OutputEmail,buf);
         fixnone(OutputEmail);
         return;
      }

      if(strstr(buf,"per_user_limit") != 0) {
         getword(wbuf,buf,' ');
         getword(PerUserLimitFile,buf,' ');
         strcpy(PerUserLimit,buf);
         fixnone(PerUserLimitFile);
         fixnone(PerUserLimit);
         return;
      }


      if(strstr(buf,"lastlog") != 0) {
         getword(wbuf,buf,' ');
         strcpy(LastLog,buf);
         fixnone(LastLog);
         return;
      }

      if(strstr(buf,"remove_temp_files") != 0) {
         getword(wbuf,buf,' ');
         strcpy(RemoveTempFiles,buf);
         fixnone(RemoveTempFiles);
         return;
      }

      if(strstr(buf,"replace_index") != 0) {
         getword(wbuf,buf,' ');
         strcpy(ReplaceIndex,buf);
         fixnone(ReplaceIndex);
         return;
      }      

      if(strstr(buf,"index_tree") != 0) {
         getword(wbuf,buf,' ');
         strcpy(IndexTree,buf);
         fixnone(IndexTree);
         return;
      }      

      if(strstr(buf,"index") != 0) {
         if(strstr(buf,"index_sort_order") == 0) {
            getword(wbuf,buf,' ');
            strcpy(Index,buf);
            fixnone(Index);
            return;
         }
      }

      if(strstr(buf,"overwrite_report") != 0) {
         getword(wbuf,buf,' ');
         strcpy(OverwriteReport,buf);
         fixnone(OverwriteReport);
         return;
      }

      if(strstr(buf,"records_without_userid") != 0) {
         getword(wbuf,buf,' ');
         strcpy(RecordsWithoutUser,buf);
         fixnone(RecordsWithoutUser);
         return;
      }

      if(strstr(buf,"use_comma") != 0) {
         getword(wbuf,buf,' ');
         strcpy(UseComma,buf);
         fixnone(UseComma);
         return;
      }

      if(strstr(buf,"mail_utility") != 0) {
         getword(wbuf,buf,' ');
         strcpy(MailUtility,buf);
         fixnone(MailUtility);
         return;
      }

      if(strstr(buf,"topsites_num") != 0) {
         getword(wbuf,buf,' ');
         strcpy(TopSitesNum,buf);
         fixnone(TopSitesNum);
         return;
      }

      if(strstr(buf,"topuser_num") != 0) {
         getword(wbuf,buf,' ');
         strcpy(TopUsersNum,buf);
         fixnone(TopUsersNum);
         return;
      }

      if(strstr(buf,"usertab") != 0) {
         getword(wbuf,buf,' ');
         strcpy(UserTabFile,buf);
         fixnone(UserTabFile);
         return;
      }

      if(strstr(buf,"index_sort_order") != 0) {
         getword(wbuf,buf,' ');
         strcpy(IndexSortOrder,buf);
         fixnone(IndexSortOrder);
         return;
      }

      if(strstr(buf,"topsites_sort_order") != 0) {
         getword(wbuf,buf,' ');
         getword(TopsitesSortField,buf,' ');
         strcpy(TopsitesSortType,buf);
         fixnone(TopsitesSortField);
         fixnone(TopsitesSortType);
         return;
      }

      if(strstr(buf,"long_url") != 0) {
         getword(wbuf,buf,' ');
         strcpy(LongUrl,buf);
         fixnone(LongUrl);
         return;
      }

      if(strstr(buf,"language") != 0) {
         getword(wbuf,buf,' ');
         strcpy(language,buf);
         fixnone(language);
         return;
      }

      if(strstr(buf,"dansguardian_conf") != 0) {
         getword(wbuf,buf,' ');
         strcpy(DansGuardianConf,buf);
         fixnone(DansGuardianConf);
         return;
      }

      if(strstr(buf,"squidguard_conf") != 0) {
         getword(wbuf,buf,' ');
         strcpy(SquidGuardConf,buf);
         fixnone(SquidGuardConf);
         return;
      }

      if(strstr(buf,"date_time_by") != 0) {
         getword(wbuf,buf,' ');
         strcpy(datetimeby,buf);
         fixnone(datetimeby);
         return;
      }

      if(strstr(buf,"charset") != 0) {
         getword(wbuf,buf,' ');
         strcpy(CharSet,buf);
         fixnone(CharSet);
         ccharset(CharSet);
         return;
      }

      if(strstr(buf,"user_invalid_char") != 0) {
         getword(wbuf,buf,'"');
         getword(UserInvalidChar,buf,'"');
         fixnone(UserInvalidChar);
         return;
      }

      if(strstr(buf,"include_users") != 0) {
         getword(wbuf,buf,'"');
         getword(wbuf,buf,'"');
	 sprintf(IncludeUsers,":%s:",wbuf);
         fixnone(IncludeUsers);
         return;
      }

      if(strstr(buf,"exclude_string") != 0) {
         getword(wbuf,buf,'"');
         getword(ExcludeString,buf,'"');
         fixnone(ExcludeString);
         return;
      }

      if(strstr(buf,"privacy") != 0) {
         if(strstr(buf,"privacy_string") == 0 && \
	    strstr(buf,"privacy_string_color") == 0) {
            getword(wbuf,buf,' ');
            strcpy(Privacy,buf);
            fixnone(Privacy);
            return;
	 }
      }

      if(strstr(buf,"privacy_string") != 0) {
	 if(strstr(buf,"privacy_string_color") == 0) {
            getword(wbuf,buf,'"');
            getword(PrivacyString,buf,'"');
            fixnone(PrivacyString);
            return;
	 }
      }

      if(strstr(buf,"privacy_string_color") != 0) {
         getword(wbuf,buf,' ');
         strcpy(PrivacyStringColor,buf);
         fixnone(PrivacyStringColor);
         return;
      }

      if(strstr(buf,"show_successful_message") != 0) {
         getword(wbuf,buf,' ');
         strcpy(SuccessfulMsg,buf);
         fixnone(SuccessfulMsg);
         return;
      }

      if(strstr(buf,"show_read_statistics") != 0) {
         getword(wbuf,buf,' ');
         strcpy(ShowReadStatistics,buf);
         fixnone(ShowReadStatistics);
         return;
      }

      if(strstr(buf,"topuser_fields") != 0) {
         getword(wbuf,buf,' ');
         strcpy(TopUserFields,buf);
         fixnone(TopUserFields);
         return;
      }

      if(strstr(buf,"bytes_in_sites_users_report") != 0) {
         getword(wbuf,buf,' ');
         strcpy(BytesInSitesUsersReport,buf);
         fixnone(BytesInSitesUsersReport);
         return;
      }

      if(strstr(buf,"user_report_fields") != 0) {
         getword(wbuf,buf,' ');
         strcpy(UserReportFields,buf);
         fixnone(UserReportFields);
         return;
      }

      if(strstr(buf,"bytes_in_sites_users_report") != 0) {
         getword(wbuf,buf,' ');
         strcpy(BytesInSitesUsersReport,buf);
         fixnone(BytesInSitesUsersReport);
         return;
      }

      if(strstr(buf,"datafile ") != 0) {
         getword(wbuf,buf,' ');
         strcpy(DataFile,buf);
         fixnone(DataFile);
         return;
      }

      if(strstr(buf,"datafile_delimiter") != 0) {
         getword(wbuf,buf,' ');
         getword(wbuf,buf,'"');
         getword(DataFileDelimiter,buf,'"');
         fixnone(DataFileDelimiter);
         return;
      }

      if(strstr(buf,"datafile_fields") != 0) {
         getword(wbuf,buf,' ');
         strcpy(DataFileFields,buf);
         fixnone(DataFileFields);
         return;
      }

      if(strstr(buf,"datafile_url") != 0) {
         getword(wbuf,buf,' ');
         strcpy(DataFileUrl,buf);
         fixnone(DataFileUrl);
         return;
      }

      if(strstr(buf,"parsed_output_log") != 0) {
	 if(strstr(buf,"parsed_output_log_compress") == 0) {
            getword(wbuf,buf,' ');
            strcpy(ParsedOutputLog,buf);
            fixnone(ParsedOutputLog);
            return;
         }
      }

      if(strstr(buf,"parsed_output_log_compress") != 0) {
         getword(wbuf,buf,' ');
         strcpy(ParsedOutputLogCompress,buf);
         fixnone(ParsedOutputLogCompress);
         return;
      }

      if(strstr(buf,"displayed_values") != 0) {
         getword(wbuf,buf,' ');
         strcpy(DisplayedValues,buf);
         fixnone(DisplayedValues);
         return;
      }

      if(strstr(buf,"authfail_report_limit") != 0) {
         getword(wbuf,buf,' ');
         AuthfailReportLimit=atoi(buf);
         return;
      }

      if(strstr(buf,"denied_report_limit") != 0) {
         getword(wbuf,buf,' ');
         DeniedReportLimit=atoi(buf);
         return;
      }

      if(strstr(buf,"siteusers_report_limit") != 0) {
         getword(wbuf,buf,' ');
         SiteUsersReportLimit=atoi(buf);
         return;
      }

      if(strstr(buf,"dansguardian_report_limit") != 0) {
         getword(wbuf,buf,' ');
         DansGuardianReportLimit=atoi(buf);
         return;
      }

      if(strstr(buf,"squidguard_report_limit") != 0) {
         getword(wbuf,buf,' ');
         SquidGuardReportLimit=atoi(buf);
         return;
      }

      if(strstr(buf,"user_report_limit") != 0) {
         getword(wbuf,buf,' ');
         UserReportLimit=atoi(buf);
         return;
      }

      if(strstr(buf,"download_report_limit") != 0) {
         getword(wbuf,buf,' ');
         DownloadReportLimit=atoi(buf);
         return;
      }

      if(strstr(buf,"www_document_root") != 0) {
         getword(wbuf,buf,' ');
         strcpy(wwwDocumentRoot,buf);
         fixnone(wwwDocumentRoot);
         return;
      }

      if(strstr(buf,"block_it") != 0) {
         getword(wbuf,buf,' ');
         strcpy(BlockIt,buf);
         fixnone(BlockIt);
         return;
      }

      if(strstr(buf,"external_css_file") != 0) {
         getword(wbuf,buf,' ');
         strcpy(ExternalCSSFile,buf);
         fixnone(ExternalCSSFile);
         return;
      }

      if(strstr(buf,"user_authentication") != 0) {
         getword(wbuf,buf,' ');
         strcpy(UserAuthentication,buf);
         fixnone(UserAuthentication);
         return;
      }

      if(strstr(buf,"AuthUserFile") != 0) {
         getword(wbuf,buf,' ');
         strcpy(AuthUserFile,buf);
         fixnone(AuthUserFile);
         return;
      }

      if(strstr(buf,"AuthName") != 0) {
         getword(wbuf,buf,' ');
         strcpy(AuthName,buf);
         fixnone(AuthName);
         return;
      }

      if(strstr(buf,"AuthType") != 0) {
         getword(wbuf,buf,' ');
         strcpy(AuthType,buf);
         fixnone(AuthType);
         return;
      }

      if(strstr(buf,"Require") != 0) {
         getword(wbuf,buf,' ');
         strcpy(Require,buf);
         fixnone(Require);
         return;
      }

      if(strstr(buf,"download_suffix") != 0) {
         getword(wbuf,buf,'"');
         getword(DownloadSuffix,buf,'"');
         fixnone(DownloadSuffix);
         return;
      }

      if(strstr(buf,"graphs") != 0) {
         getword(wbuf,buf,' ');
         strcpy(Graphs,buf);
         fixnone(Graphs);
         return;
      }

      if(strstr(buf,"graph_days_bytes_bar_color") != 0) {
         getword(wbuf,buf,' ');
         strcpy(GraphDaysBytesBarColor,buf);
         fixnone(GraphDaysBytesBarColor);
         return;
      }

      if(strstr(buf,"squidguard_log_format") != 0) {
         getword(wbuf,buf,' ');
         strcpy(SquidGuardLogFormat,buf);
         fixnone(SquidGuardLogFormat);
         return;
      }

      if(strstr(buf,"squidguard_ignore_date") != 0) {
         getword(wbuf,buf,' ');
         strcpy(SquidguardIgnoreDate,buf);
         fixnone(SquidguardIgnoreDate);
         return;
      }

      if(strstr(buf,"dansguardian_ignore_date") != 0) {
         getword(wbuf,buf,' ');
         strcpy(DansguardianIgnoreDate,buf);
         fixnone(DansguardianIgnoreDate);
         return;
      }

      if(strstr(buf,"ulimit") != 0) {
         getword(wbuf,buf,' ');
         strcpy(Ulimit,buf);
         fixnone(Ulimit);
         return;
      }

      if(strstr(buf,"ntlm_user_format") != 0) {
         getword(wbuf,buf,' ');
         strcpy(NtlmUserFormat,buf);
         fixnone(NtlmUserFormat);
         return;
      }

      if(strstr(buf,"realtime_types") != 0) {
         getword(wbuf,buf,' ');
         strcpy(RealtimeTypes,buf);
         fixnone(RealtimeTypes);
         return;
      }

      if(strstr(buf,"realtime_unauthenticated_records") != 0) {
         getword(wbuf,buf,' ');
         strcpy(RealtimeUnauthRec,buf);
         fixnone(RealtimeUnauthRec);
         return;
      }

      if(strstr(buf,"realtime_refresh_time") != 0) {
         getword(wbuf,buf,' ');
         realtime_refresh=atoi(buf);
         return;
      }

      if(strstr(buf,"realtime_access_log_lines") != 0) {
         getword(wbuf,buf,' ');
         realtime_access_log_lines=atoi(buf);
         return;
      }

      if(strstr(buf,"byte_cost") != 0) {
         getword(wbuf,buf,' ');
         cost=atol(buf);
         getword(wbuf,buf,' ');
         nocost=my_atoll(buf);
         return;
      }
}

void getconf()
{

   FILE *fp_in;
   char buf[MAXLEN];

   if(debug) {
      sprintf(Msg,"Loading configuration from: %s",ConfigFile);
      debuga(Msg);
   }

   if ((fp_in = fopen(ConfigFile, "r")) == NULL) {
      fprintf(stderr, "SARG: (getconf) Cannot open file: %s\n",ConfigFile);
      exit(1);
   }

   while (fgets(buf, MAXLEN, fp_in) != NULL) {
      if(strstr(buf,"\n") != 0)
         buf[strlen(buf)-1]='\n';

      if(debugm)
         printf("SYSCONFDIR %s",buf);

      if(strncmp(buf,"#",1) == 0 || strncmp(buf,"\n",1) == 0)
         continue;

      if(debugz)
         printf("SARG: TAG: %s",buf);

      parmtest(buf);

   }

   fclose(fp_in);
   language_load(language);
   return;
}

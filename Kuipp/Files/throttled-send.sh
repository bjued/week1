#!/bin/bash
################################################################################
# Wrapper around the Amazon SES ses-send-email.pl program. This script checks
# the timestamp of a file which indicates when an email was last sent, with the
# purpose of delaying sending the email if the throttling period has not yet
# expired between this message and the previous one.
#
# By Leandro Baca of S&H Switzerland (http://www.s-h.ch/)
################################################################################

#--- CONFIGURABLE VARIABLES ----------------------------------------------------

# Amazon SES send email program (full path), with all arguments that would be
# used in /etc/postfix/master.cf, except for ${sender} and ${recipient}, which
# will be passed to this script. Be sure to use the -r option to indicate a raw
# email format.
send_program="/opt/third-party/amazon/ses-send-email.pl -r -k /opt/third-party/amazon/aws-credentials -e https://email.us-east-1.amazonaws.com -f"

# Number of seconds to leave between two sent emails. Note that fractions of a
# second are not checked, so if the Amazon-imposed blackout period is 1 second,
# it would not be safe to leave less than 2.
blackout=2

# Timestamp file (full path). This is the file whose timestamp will report to
# this script when the previous email was sent, and the timestamp will be
# updated with each sent email. If the file doesn't exist, it will be treated
# as if no emails had ever been sent (and hence no blackout). If you pre-create
# the file before running this script for the first time through Postfix, make
# sure the Postfix user (i.e., the one configured in /etc/postfix/master.cf) has
# write access to it.
tsfile=/opt/third-party/amazon/.last-sent

#--- NO CHANGES BELOW THIS LINE ------------------------------------------------

# Initialize return code to error
retcode=1

# This script needs to be called with at least a sender and one recipient
if [ "$#" -ge "2" ]; then
  # The first argument is the sender; all others are recipients
  sender="${1}"
  shift

  # Create temporary file for storing the raw message data from stdin. This is
  # necessary because if there's more than one loop, the stdin data will only be
  # available to the first loop (is this correct??). If temporary file creation
  # fails for some reason, abort the script.
  tmpfile=`mktemp -q /tmp/mail.XXXXXXXXXX` || exit ${retcode}
  tee "${tmpfile}"

  # Loop for each email recipient
  for recipient in "$@"; do
    # If the timestamp file exists, check if enough time has passed between sent
    # emails. All these checks use the POSIX Epoch format because it's the only
    # truly arithmetic way to compare time offsets.
    if [ -f "${tsfile}" ]; then
      # Timestamp of last sent email
      last_sent=`date -r "${tsfile}" -u +%s`

      # The earliest timestamp at which the next email can be sent
      next_send=`expr ${last_sent} + ${blackout}`

      # The current time
      now=`date -u +%s`

      # If the next earliest email sending timestamp is in the future, pause
      # for ${blackout} seconds. Note that if the timestamp file is fiddled with
      # such that the "last sent email timestamp" is actually far in the future,
      # the script will still only pause for ${blackout} seconds, and the touch
      # below will reset the timestamp accordingly.
      if [ "${next_send}" -gt "${now}" ]; then
        sleep ${blackout}s
      fi
    fi

    # Send the email to a single recipient, taking the input from the previously
    # captured stdin
    ${send_program} "${sender}" "${recipient}" < "${tmpfile}"

    # Store return code from email sending operation
    retcode=$?

    # Regardless of success or failure, set the timestamp. I'm not sure if a
    # failed send actually impacts SES's throttling (maybe it actually depends
    # on the type of error?), but this is the safest approach.
    touch "${tsfile}"

    # If the previous send operation failed, don't continue looping for other
    # recipients
    if [ "${retcode}" -gt "0" ]; then
      break
    fi
  done

  # Delete the temporary file
  rm -f "${tmpfile}"
fi

# Exit with the appropriate return code
exit ${retcode}

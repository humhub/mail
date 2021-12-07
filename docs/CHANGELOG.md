Changelog
=========

2.1.0  (December 7, 2021)
-------------------------
- Enh #564: Restrict messaging for blocked users
- Enh #232: Render images in email notifications
- Fix #224: Fix preview message in sidebar
- Fix #231: CLI error when no REST module is installed
- Fix #230: Fix notifications of new Conversation vs Message entry
- Fix #240: Fix apostrophe encode in mail

2.0.7  (April 8, 2021)
----------------------
- Fix #221: Fix call of console commands when REST API module doesn't exist

2.0.6  (April 8, 2021)
----------------------
- Enh: Use controller config for not intercepted actions
- Enh #217: RESTFul API Module Support 

2.0.5 - January 21, 2021
------------------------
- Fix: Check SendMail permission on action (Special thanks to @jrckmcsb for the security audit)

2.0.4 - November 05, 2020
-----------------------
- Fix: Error thorwn if no lastEntry of conversation could be found
- Fix: Script error logged in case conversation message list not found

2.0.3 - November 05, 2020
-----------------------
- Fix #203: User deletion does not delete conversations created by this user
- Enh: Added integrity check for invalid user ids

2.0.2 - November 02, 2020
-----------------------
- Fix: Removed asset forceCopy

2.0.1 - November 02, 2020
-----------------------
- Fix #201: Error thrown after update due to update process race condition
- Fix #202: Editor overlaps messages
- Enh: Updated translations
- Fix #194: Message entry can not be deleted
- Fix #163: Mail notification link not working if pjax is disabled

2.0.0 - October 27, 2020
-----------------------
- Fix #199 Block code overflows message entry
- Fix #198 Conversation view not scrolling down completely
- Fix: Use of ResizeObserver for detecting richtext size changes
- Fix: Richtext resize delay
- Fix #200: Mobile scrolling broken

2.0.0-beta.2 - September 11, 2020
-----------------------
- Enh: Added fail safe drop table

2.0.0-beta.1 - September 11, 2020
-----------------------
- Enh: Use of infinite scrolling for inbox and conversation view
- Enh: Added user message tags
- Enh: Added inbox filter
- Enh: Added max amount of displayed user images
- Enh: Added message dropdown menu
- Enh: Reworked inbox and conversation view
- Enh: Merge message sequence of a user (currently only on reload)
- Enh: Use of splitted and minified assets
- Enh: Enhanced mobile view
- Chng: Update min HumHub version to v1.6.3
- Chng: Major refactoring
- Enh: Enable default permissions
- Enh: Support RESTful API module

1.0.15 - March 28, 2020
-----------------------
- Fix #173: New message creation notification fails 


1.0.14 - March 17, 2020
-----------------------
- Chg: Internal change in message creation order
- Enh: Updated translations


1.0.13 - February 12, 2020
-----------------------
- Fix: Max. conversation check not disabled
- Enh: Updated translations


1.0.12 - October 16, 2019
-----------------------
- Enh: 1.4 nonce compatibility


1.0.11 - June 15, 2019
-----------------------
- Enh: Updated translations
- Enh: Improved docs


1.0.10 - March 27, 2019
-----------------------
- Fix: #155 Mail duplication in Mail Dropdown after repeated clicks


1.0.9 - March 14, 2019
-----------------------
- Enh: Use of `Richtext::preview` instead of `MarkdownPreview`


1.0.7 - March 11, 2019
-----------------------
- Enh: Added new conversation restrictions


1.0.6 - November 16, 2018
-----------------------
- Fix: #131 wrong norwegain translation
- Fix: #79 Sender langauge used for e-mail notification instead of receiver language
- Fix: #147 Send message button in profile missing margin


1.0.5 - November 2, 2018
-----------------------
- Enh: Updated translations
- Enh: Added font less option in e-mail templates (@rekollekt)


1.0.4 - October 5, 2018
-----------------------
- Fix: IE11 compatibility issue


1.0.3 - October 4, 2018
-----------------------
- Fix: #147 Message button size on profile


1.0.2 - September 27, 2018
-----------------------
- Fix: Files are not attached to MessageEntry


1.0.1 - September 26, 2018
-----------------------
- Fix: Message preview encoding issue


1.0.0 - September 26, 2018
-----------------------
- Enh: Live updates
- Enh: Ajaxify conversation view
- Enh: Added conversation user list to mail overview
- Chng: Major refactoring


0.9.14 - July 2, 2018
-----------------------
- Fix: PHP 7.2 compatibility issues


0.9.13 - June 13, 2018
----------------------
- Enh: Added option to hide main navigation "Message" entry


0.9.9 - May 16, 2018
----------------------
- Enh: Added global `StartConversation` permission


0.9.8 - October 4, 2017
----------------------
- Fix: #132, #129 Added Conversation and Message Notification Categories

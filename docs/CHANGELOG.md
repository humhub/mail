Changelog
=========

3.2.3 (March 5, 2025)
---------------------
- Enh #411: Standardization of Modal Button Positions
- Enh #363: Deny access for files from message where current user is not participant
- Fix #412: Fix active message after mark unread
- Fix #15: When replying to a message, the conversation is marked as unread
- Enh #417: Replace theme variables with CSS variables
- Enh #418: Update Active Form for Bootstrap 5
- Enh #422: Changed deprecated `DOMSubtreeModified` to `MutationObserver`
- Fix #423: Fix rendering of attached files on new reply by PushService
- Enh #424: Allow sending a message with attached files only, such as an image
- Enh #426: Reduce translation message categories
- Fix #428: Fixed observer missing parameter
- Fix #434: Add `cursor: pointer;` to `#conversation-settings-button`

3.2.2 (July 9, 2024)
--------------------
- Enh #382: Implement provider for meta searching
- Fix #389: Fix notification about participant joining
- Fix #396: Don't override global styles
- Enh #397: Use PHP CS Fixer
- Fix #402: Fix undefined function to load a message

3.2.1 (April 15, 2024)
----------------------
- Enh #370: Remove message entries on disable module
- Fix #385: Fix undefined conversation view

3.2.0 (January 29, 2024)
------------------------
- Enh #377: New features: Unread & Pinned
- Fix #369: Misplaced Online dot

3.1.2 (January 22, 2024)
------------------------
- Enh #353: Tests for `next` version
- Fix #354: Don't display date badge twice after update a message
- Fix #356: Fix visibility of the method `Controller::getAccessRules()`
- Fix #358: Fix sending email notification to deleted users
- Fix #361: Encoding issue
- Enh #364: Confirm before leaving a filled message form
- Enh #367: Allow message title and body be provided by `GET` request for new messages
- Enh #368: Add push notifications when FCM Push Module is active
- Fix #371: Don't send notification to deactivated user
- Fix #373: Display full name only when it is configured in general settings
- Fix #339: Remove deprecated class `humhub\widgets\MarkdownView`

3.1.1  (September 19, 2023)
---------------------------
- Fix #351: Fix a deleted user in state badge

3.1.0  (September 16, 2023)
---------------------------
- Fix #348: Remove new lines in notificationInbox view
- Fix #349: Assets now extending `humhub\components\assets\AssetBundle` instead of `yii\web\AssetBundle`
- Enh #324: Possibility to attach files to a message entry
- Enh #325: Display who is currently online

3.0.2  (August 17, 2023)
-------------------------
- Fix #345: Fix last entry with missed user

3.0.1  (August 10, 2023)
-------------------------
- Fix #343: Fix error after deleting a user from system

3.0.0  (July 26, 2023)
----------------------
- Fix #312: Visibility of scroll down button
- Fix #313: Display state(joined/left) messages in inbox
- Fix #322: Fix color of hover or active sub headline
- Fix #335: Remove new lines in notificationInbox view

3.0.0-beta.1  (March 13, 2023)
------------------------------
- Enh #283: Design Optimizations, Renamed to "Messenger"
- Fix #252: Wrong user guid in Live Notification
- Fix #251: Edit message
- Fix #283: Add markdown-render class to Markdown text for Translator module to work
- Fix #272: Exclude invisible users from recipients
- Fix #280: Update styles of message block
- Enh #274: Browser Tab Indicator on New Unread Message

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

<?php

namespace mail\api;

use mail\ApiTester;
use tests\codeception\_support\HumHubApiTestCest;

class EntryCest extends HumHubApiTestCest
{
    public function testListByConversationId(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see entries of the conversation by id');
        $I->amAdmin();
        $I->seePaginationGetResponse('mail/3/entries', [
            ['id' => 4, 'content' => 'Third Message entry text 1.', 'user_id' => 1],
            ['id' => 5, 'content' => 'Third Message entry text 2.', 'user_id' => 2],
            ['id' => 6, 'content' => 'Third Message entry text 3.', 'user_id' => 3],
        ]);
    }

    public function testGetById(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('see entry by id');
        $I->amUser1();
        $I->sendGet('mail/2/entry/3');
        $I->seeSuccessResponseContainsJson([
            'id' => 3,
            'user_id' => 2,
            'content' => 'Second Message entry text 2.',
        ]);
    }

    public function testCreateEntry(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('create entry');
        $I->amUser1();
        $newMessage = 'New sample reply for conversation #2';
        $I->sendPost('mail/2/entry', ['message' => $newMessage]);
        $I->seeSuccessResponseContainsJson([
            'id' => 7,
            'user_id' => 2,
            'content' => $newMessage,
        ]);
    }

    public function testCreateEntryByNotParticipant(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('cannot create entry by not participant');
        $I->amUser3();
        $newMessage = 'New sample reply for conversation #2';
        $I->sendPost('mail/2/entry', ['message' => $newMessage]);
        $I->seeForbiddenResponseContainsJson([
            'message' => 'You must be a participant of the conversation.',
        ]);
    }

    public function testUpdateEntry(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('update entry by id');
        $I->amAdmin();
        $updatedMessage = 'Updated content of the entry #4';
        $I->sendPut('mail/3/entry/4', ['content' => $updatedMessage]);
        $I->seeSuccessResponseContainsJson([
            'id' => 4,
            'user_id' => 1,
            'content' => $updatedMessage,
        ]);
    }

    public function testCannotUpdateEntry(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('cannot update not own entry');
        $I->amUser1();
        $updatedMessage = 'Updated content of the entry #2';
        $I->sendPut('mail/3/entry/4', ['content' => $updatedMessage]);
        $I->seeForbiddenResponseContainsJson([
            'message' => 'You cannot edit the conversation entry!',
        ]);
    }

    public function testDeleteEntry(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('delete entry');
        $I->amUser1();
        $I->sendDelete('mail/3/entry/5');
        $I->seeSuccessResponseContainsJson([
            'message' => 'Conversation entry successfully deleted!',
        ]);
    }

    public function testCannotDeleteEntry(ApiTester $I)
    {
        if (!$this->isRestModuleEnabled()) {
            return;
        }

        $I->wantTo('cannot delete not own entry');
        $I->amAdmin();
        $I->sendDelete('mail/3/entry/6');
        $I->seeForbiddenResponseContainsJson([
            'message' => 'You cannot delete the conversation entry!',
        ]);
    }
}

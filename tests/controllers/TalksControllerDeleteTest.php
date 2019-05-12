<?php

namespace JoindinTest\Controller;

use ApiView;
use JoindinTest\Inc\mockPDO;
use Request;
use TalkMapper;
use TalksController;

class TalksControllerDeleteTest extends TalkBase
{
    public function testRemoveStarFromTalkFailsWhenNotLoggedIn()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('You must be logged in to remove data');
        $this->expectExceptionCode(401);

        $request = new Request(
            [],
            [
                'REQUEST_URI' => 'http://api.dev.joind.in/v2.1/talks/79/starred',
                'REQUEST_METHOD' => 'DELETE'
            ]
        );

        $db = $this->getMockBuilder(mockPDO::class)->getMock();

        $talks_controller = new TalksController();
        $talks_controller->deleteTalkStarred($request, $db);
    }

    public function testRemoveStarFromTalksWhenLoggedIn()
    {
        $request = new Request(
            [],
            [
                'REQUEST_URI' => 'http://api.dev.joind.in/v2.1/talks/79/starred',
                'REQUEST_METHOD' => 'DELETE'
            ]
        );
        $request->user_id = 2;
        $request->parameters = [
            'username'      => 'psherman',
            'display_name'  => 'P Sherman',
        ];

        $db = $this->getMockBuilder(mockPDO::class)->getMock();

        $this->talkMapper = $this->createTalkMapper($db, $request, 0);
        $this->talkMapper->method('setUserNonStarred')
            ->willReturn(true);

        $talks_controller = new TalksController();
        $talks_controller->setTalkMapper($this->talkMapper);

        $talks_controller->deleteTalkStarred($request, $db);
    }

    public function testDeleteTalkWhenNotLoggedIn()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('You must be logged in to remove data');
        $this->expectExceptionCode(401);

        $request = new Request(
            [],
            [
                'REQUEST_URI' => 'http://api.dev.joind.in/v2.1/talks/79',
                'REQUEST_METHOD' => 'DELETE'
            ]
        );

        $db = $this->getMockBuilder(mockPDO::class)->getMock();

        $talks_controller = new TalksController();
        $talks_controller->deleteTalk($request, $db);
    }

    public function testDeleteTalkWhichDoesntExist()
    {
        $httpRequest = [
            'REQUEST_URI' => 'http://api.dev.joind.in/v2.1/talks/79',
            'REQUEST_METHOD' => 'DELETE'
        ];
        $request = $this->getMockBuilder(Request::class)
            ->setConstructorArgs([[], $httpRequest ])
            ->getMock();

        $request->user_id = 2;
        $request->parameters = [
            'username'      => 'psherman',
            'display_name'  => 'P Sherman',
        ];

        $db = $this->getMockBuilder(mockPDO::class)->getMock();

        $this->talk_mapper
            ->method('getTalkById')
            ->willReturn(false);

        $talks_controller = new TalksController();
        $talks_controller->setTalkMapper($this->talk_mapper);

        $view = $this->getMockBuilder(ApiView::class)->getMock();
        $request->method('getView')->willReturn($view);

        $view->method('setHeader')->with('Content-Length', 0);
        $view->method('setResponseCode')->with(204);

        $this->assertNull($talks_controller->deleteTalk($request, $db));
    }

    public function testDeleteTalkWthNoAdmin()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('You do not have permission to do that');
        $this->expectExceptionCode(400);

        $request = new Request(
            [],
            [
                'REQUEST_URI' => 'http://api.dev.joind.in/v2.1/talks/79',
                'REQUEST_METHOD' => 'DELETE'
            ]
        );

        $request->user_id = 2;
        $request->parameters = [
            'username'      => 'psherman',
            'display_name'  => 'P Sherman',
        ];

        $db = $this->getMockBuilder(mockPDO::class)->getMock();

        $talk_mapper = $this->createTalkMapper($db, $request);

        $talks_controller = new TalksController();
        $talks_controller->setTalkMapper($talk_mapper);


        $talks_controller->deleteTalk($request, $db);
    }

    public function testDeleteTalkWithAdmin()
    {
        $httpRequest = [
            'REQUEST_URI' => 'http://api.dev.joind.in/v2.1/talks/79',
            'REQUEST_METHOD' => 'DELETE'
        ];
        $request = $this->getMockBuilder(Request::class)
            ->setConstructorArgs([[], $httpRequest ])
            ->getMock();

        $request->user_id = 2;
        $request->parameters = [
            'username'      => 'psherman',
            'display_name'  => 'P Sherman',
        ];

        $db = $this->getMockBuilder(mockPDO::class)->getMock();

        $this->talk_mapper
            ->method('thisUserHasAdminOn')
            ->willReturn(true);

        $talks_controller = new TalksController();
        $talks_controller->setTalkMapper($this->talk_mapper);

        $view = $this->getMockBuilder(ApiView::class)->getMock();
        $request->method('getView')->willReturn($view);

        $view->method('setHeader')->with('Content-Length', 0);
        $view->method('setResponseCode')->with(204);

        $this->assertNull($talks_controller->deleteTalk($request, $db));
    }
}

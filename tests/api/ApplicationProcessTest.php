<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Portal\Jobs\SendContractToUserEmail;
use MailThief\Testing\InteractsWithMail;

class ApplicationProcessTest extends Tests\TestCase
{

    use DatabaseMigrations;
    use DatabaseTransactions;

   // Provides convenient testing traits and initializes MailThief
    use InteractsWithMail;

    /**
     * Setup the tests on this page
     */
    public function setUp()
    {
        parent::setUp();

        $this->user = factory( Portal\User::class )->create( [
            'role' => 'application'
        ] );

        $this->userTenant = factory( Portal\User::class )->create( [
            'role' => 'tenant'
        ] );

        factory( Portal\Location::class, 5 )->create( [
            'id' => Uuid::generate()->string
        ] )->each( function ( $l ) {

            factory( Portal\UnitType::class, 5 )->create( [
                'location_id' => $l->id
            ] )->each( function ( $u ) {

                factory( Portal\Unit::class, 20 )->create( [
                    'location_id' => $u->location_id,
                    'type_id'     => $u->id
                ] );
            } );
        } );

        $location = \Portal\Location::all()->first();

        $this->application = factory(Portal\Application::class)->states('forApproval')->create([
            'user_id' => $this->userTenant->id,
            'unit_location' => $location->id,
            'unit_type' => $location->unitTypes->first()->id
        ]);


    }

    /**
     * Test that a user can view the page
     */
    public function testCanSeeReviewPage()
    {

        $this->actingAs( $this->user )
            ->visit('/application/'. $this->application->id.'/review')
            ->assertResponseStatus(200);

    }

    /*
     * Test an contract submission fails on user id validation
     */
    public function testFailDeclineApplicationWithoutReason()
    {

        $this->actingAs( $this->user )
            ->json( 'POST', '/application/' . $this->application->id . '/decline', [
                'php' => ''
            ] )
            ->assertResponseStatus( 422 )
            ->seeJson( [
                "reason" => [ "The reason field is required." ]
            ] );

    }

    /**
     * Test that a declined application with a reason
     * was accepted and that an event was created
     */
    public function testDeclineApplication()
    {

        $this->actingAs( $this->user )
            ->json( 'POST', '/application/' . $this->application->id . '/decline', [
                'reason' => 'This is the reason'
            ] )
            ->assertResponseStatus( 200 )

            ->seeInDatabase( 'applications', [
                'id' => $this->application->id,
                'status' => 'declined'
            ] )
            ->seeInDatabase( 'application_events', [
                'application_id' => $this->application->id,
                'user_id' => $this->user->id,
                'action' => 'Application declined',
                'note' => 'This is the reason'
            ] );

    }

    /**
     * Check email sent is correct
     */
    public function testDeclinedApplicationEmailContents()
    {

        $this->actingAs( $this->user )
            ->json( 'POST', '/application/' . $this->application->id . '/decline', [
                'reason' => 'This is the reason'
            ] )
            ->assertResponseStatus( 200 );

        $this->seeMessageFor($this->userTenant->email);
        $this->seeMessageWithSubject('Your application has been declined');
        $this->seeMessageFrom('info@mydomainliving.co.za');
        $this->assertTrue($this->lastMessage()->contains('Your application for accommodation at My Domain Southern Suburbs has been declined'));

    }

    /**
     * Test that a user can view the pending
     */
    public function testCanSeePendingPage()
    {

        $this->actingAs( $this->user )
            ->visit('/application/'. $this->application->id.'/pending')
            ->assertResponseStatus(200);

    }

    /**
     * Test that a pending application process fails validation
     */
    public function testPendingApplicationFailsWithoutReason()
    {

        $this->actingAs( $this->user )
            ->json( 'POST', '/application/' . $this->application->id . '/pending', [
                'reason' => ''
            ] )
            ->assertResponseStatus( 422 );
    }

    /**
     * Test that a pending application passes
     */
    public function testPendingApplication()
    {

        $this->actingAs( $this->user )
            ->json( 'POST', '/application/' . $this->application->id . '/pending', [
                'reason' => 'This is the reason'
            ] )
            ->assertResponseStatus( 200 )
            ->seeInDatabase( 'applications', [
                'id' => $this->application->id,
                'status' => 'pending'
            ] )
            ->seeInDatabase( 'application_events', [
                'application_id' => $this->application->id,
                'user_id' => $this->user->id,
                'action' => 'Application pending',
                'note' => 'This is the reason'
            ] );
    }

    /**
     * Check email sent is correct
     */
    public function testPendingApplicationEmailContents()
    {

        $this->actingAs( $this->user )
            ->json( 'POST', '/application/' . $this->application->id . '/pending', [
                'reason' => 'This is the reason'
            ] )
            ->assertResponseStatus( 200 );

        $this->seeMessageFor($this->userTenant->email);
        $this->seeMessageWithSubject('Your application is pending');
        $this->seeMessageFrom('info@mydomainliving.co.za');
        $this->assertTrue($this->lastMessage()->contains('Your application has been marked pending.'));
        $this->assertTrue($this->lastMessage()->contains('The reason for the application being marked as pending is: This is the reason'));

    }

    /**
     * Test that a user can view the approve page
     */
    public function testCanSeeApprovePage()
    {

        $this->actingAs( $this->user )
            ->visit('/application/'. $this->application->id.'/approve')
            ->assertResponseStatus(200);

    }

}
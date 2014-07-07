//
//  EasyAppAppDelegate.m
//  EasyApp
//
//  Created by Tapjoy.
//  Copyright 2010. Tapjoy.com  All rights reserved.
//

#import "EasyAppAppDelegate.h"
#import "TapjoyConnect.h"

@implementation EasyAppAppDelegate

@synthesize window;

- (void)applicationDidFinishLaunching:(UIApplication *)application 
{	
	[[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(tjcConnectSuccess:) name:TJC_CONNECT_SUCCESS object:nil];
	[[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(tjcConnectFail:) name:TJC_CONNECT_FAILED object:nil];
	
	// NOTE: This must be replaced by your App Id. It is Retrieved from the Tapjoy website, in your account.
    [TapjoyConnect requestTapjoyConnect:@"93e78102-cbd7-4ebf-85cc-315ba83ef2d5" secretKey:@"JWxgS26URM0XotaghqGn"];

	msgLabel.text = @"Tapjoy Connect Test";
	statusLabel.text = @"Waiting For Response From Server...";
	
	[window makeKeyAndVisible];
}


- (void)dealloc 
{
    [window release];
	[msgLabel release];
	
    [super dealloc];
}

#pragma mark TapjoyConnect Observer methods

-(void) tjcConnectSuccess:(NSNotification*)notifyObj
{
	NSLog(@"Tapjoy Connect Succeeded");
	
	statusLabel.text = @"Tapjoy Connect Succeeded";
}

-(void) tjcConnectFail:(NSNotification*)notifyObj
{
	NSLog(@"Tapjoy Connect Failed");
	
	statusLabel.text = @"Tapjoy Connect Failed";
}

@end

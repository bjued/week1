//
//  EasyAppAppDelegate.h
//  EasyApp
//
//  Created by Tapjoy.
//  Copyright 2010. Tapjoy.com  All rights reserved.
//

#import <UIKit/UIKit.h>

@interface EasyAppAppDelegate : NSObject <UIApplicationDelegate> 
{
    UIWindow *window;
	IBOutlet UILabel *msgLabel;
	IBOutlet UILabel *statusLabel;
}

@property (nonatomic, retain) IBOutlet UIWindow *window;

@end


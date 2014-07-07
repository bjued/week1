//
//  HelloWorldScene.h
//  zoneControl
//
//  Created by Brandon Jue on 6/16/14.
//  Copyright bjued 2014. All rights reserved.
//
// -----------------------------------------------------------------------

#import "cocos2d.h"
#import "cocos2d-ui.h"
#import "ZCGrid.h"

@class ZCGrid;
@interface HelloWorldScene : CCScene <ZCGridDelegate> {
	CCScrollView *_scrollView;
	
	NSMutableArray *_teamColors;
	NSMutableArray *_teamSymbols;
	NSMutableArray *_startLocations;
	
	ZCGrid *_grid;
	NSMutableArray *_teams;
	NSMutableArray *_scores;
	
	int _tickDelta;
	CCTime _elapsed;
	CCTime _nextTick;
}

+ (HelloWorldScene *)scene;
- (id)init;

@end
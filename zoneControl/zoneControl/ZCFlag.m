//
//  ZCFlag.m
//  zoneControl
//
//  Created by Brandon Jue on 6/17/14.
//  Copyright 2014 bjued. All rights reserved.
//

#import "ZCFlag.h"
#import "ZCGrid.h"
#import "ZCTeam.h"

@implementation ZCFlag

+ (instancetype)flagTeam:(ZCTeam*)team {
	return [[ZCFlag alloc]initWithTeam:team];
}

- (id)initWithTeam:(ZCTeam*)team {
    self = [super init];
    if (!self) return nil;
	
//	self.anchorPoint = ccp(0.5, 0.5);
	self.type = ZCCellTypeFlag;
	self.teamId = team.teamId;
	
    CCSprite *flag = [CCSprite spriteWithImageNamed:@"flag_base.png"];
	[self addChild:flag];
	
    CCSprite *colorPart = [CCSprite spriteWithImageNamed:@"flag_colored.png"];
	colorPart.color = team.color;
	[self addChild:colorPart];
	[_colored addObject:colorPart];
	
	CCSprite *symbol = [CCSprite spriteWithImageNamed:team.symbolName];
	[self addChild:symbol];
	
	self.scale = (ZCTileSize * 2.0) / 30.0;
	
	return self;
}

@end

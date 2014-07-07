//
//  ZCBase.m
//  zoneControl
//
//  Created by Brandon Jue on 6/16/14.
//  Copyright 2014 bjued. All rights reserved.
//

#import "ZCBase.h"
#import "ZCGrid.h"
#import "ZCTeam.h"

@implementation ZCBase

+ (instancetype)baseTeam:(ZCTeam*)team {
	return [[ZCBase alloc]initWithTeam:team];
}

- (id)initWithTeam:(ZCTeam*)team {
    self = [super init];
    if (!self) return nil;
	
	self.anchorPoint = ccp(0.5, 0.5);
	self.type = ZCCellTypeBase;
	self.teamId = team.teamId;
	self.health = 1000;
	
    CCSprite *base = [CCSprite spriteWithImageNamed:@"house_base.png"];
	[self addChild:base];
	
    CCSprite *colorPart = [CCSprite spriteWithImageNamed:@"house_colored.png"];
	colorPart.color = team.color;
	[self addChild:colorPart];
	
	[_colored addObject:colorPart];
	
	self.scale = (ZCTileSize * 2.0) / 16.0;
	
	return self;
}

@end

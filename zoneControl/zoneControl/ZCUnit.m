//
//  ZCUnit.m
//  zoneControl
//
//  Created by Brandon Jue on 6/16/14.
//  Copyright 2014 bjued. All rights reserved.
//

#import "ZCUnit.h"
#import	"ZCGrid.h"
#import "ZCTeam.h"

@implementation ZCUnit
@synthesize attackPattern = _attackPattern;
@synthesize movePattern = _movePattern;
@synthesize gridPosition = _gridPosition;;

+ (instancetype)unitTeam:(ZCTeam *)team {
	return [[ZCUnit alloc]initWithTeam:team];
}

- (id)initWithTeam:(ZCTeam*)team {
    self = [super init];
    if (!self) return nil;
	
	self.anchorPoint = ccp(0.5, 0.5);
	self.type = ZCCellTypeUnit;
	self.teamId = team.teamId;
	
	self.scale = ZCTileSize / 64.0;
	
	NSDictionary *unitDict = [ZCData shared].units[@((int)team.unitType).description];
	self.health = [[unitDict objectForKey:@"health"] intValue];
	_attack = [[unitDict objectForKey:@"attack"] intValue];
	self.movePattern = [unitDict objectForKey:@"movePattern"];
	self.attackPattern = [unitDict objectForKey:@"attackPattern"];
	
	NSArray *sprite = [unitDict objectForKey:@"sprite"];
	
	for (NSArray *pair in sprite) {
		CCSprite *image = [CCSprite spriteWithImageNamed:pair[0]];
		[self addChild:image];
		if ([pair[1] boolValue]) {
			image.color = team.color;
			[_colored addObject:image];
		}
	}
	
	return self;
}

- (int)attack {
	return _attack;
}

@end

//
//  ZCCell.m
//  zoneControl
//
//  Created by Brandon Jue on 6/17/14.
//  Copyright 2014 bjued. All rights reserved.
//

#import "ZCCell.h"
#import "ZCTeam.h"

@implementation ZCCell
@synthesize type = _type;
@synthesize teamId = _teamId;
@synthesize health = _health;

- (id)init {
	self = [super init];
	if (!self) return nil;
	
	_colored = [NSMutableArray array];
	return self;
}

- (void)destroy {
	for (CCSprite *sprite in _colored) {
		sprite.color = [CCColor darkGrayColor];
	}
}

- (NSString*)address {
	return [NSString stringWithFormat:@"%p", self];
}

@end

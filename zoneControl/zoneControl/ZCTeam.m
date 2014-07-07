//
//  ZCTeam.m
//  zoneControl
//
//  Created by Brandon Jue on 6/16/14.
//  Copyright (c) 2014 bjued. All rights reserved.
//

#import "ZCTeam.h"
#import "ZCBase.h"
#import "ZCUnit.h"
#import "ZCFlag.h"

@implementation ZCTeam
@synthesize teamId = _teamId;
@synthesize bases = _bases;
@synthesize units = _units;
@synthesize flag = _flag;
@synthesize scoreLabel = _scoreLabel;
@synthesize symbolName = _symbolName;
@synthesize color = _color;
@synthesize unitType = _unitType;

+ (instancetype)teamId:(int)teamId color:(CCColor*)color symbolName:(NSString*)sym {
	return [[ZCTeam alloc]initWithId:teamId color:color symbolName:sym];
}

- (id)initWithId:(int)teamId color:(CCColor*)color symbolName:(NSString*)sym {
    self = [super init];
    if (!self) return nil;
	
	self.teamId = teamId;
	self.bases = [NSMutableDictionary dictionary];
	self.units = [NSMutableDictionary dictionary];
	self.symbolName = sym;
	self.color = color;
	
	self.unitType = ZCUnitTypeKnightLance;
	
	self.flag = [ZCFlag flagTeam:self];
    ZCBase *firstBase = [ZCBase baseTeam:self];
	
	_bases[firstBase.address] = firstBase;
	
	_score = 0;
	self.scoreLabel = [CCLabelTTF labelWithString:@(_score).description fontName:@"Chalkduster" fontSize:18];
	self.scoreLabel.color = color;
	self.scoreLabel.contentSize = CGSizeMake(100, 25);
	self.scoreLabel.horizontalAlignment = CCTextAlignmentRight;
	
	return self;
}

- (void)removeBase:(ZCBase*)base {
	if (!base.teamId == _teamId) return;
	
	[_bases removeObjectForKey:base.address];
	if (_bases.count < 1) {
		for (ZCUnit *unit in [_units allValues]) {
			[unit destroy];
		}
		[_units removeAllObjects];
		[_flag removeFromParentAndCleanup:YES];
	}
}
- (void)addKills:(int)kills {
	_score += kills;
	_scoreLabel.string = @(_score).description;
	
	if (_score > 200) _unitType = ZCUnitTypeWizard;
	else if (_score > 150) _unitType = ZCUnitTypeKnightSword;
	else if (_score > 100) _unitType = ZCUnitTypeArcher;
	else if (_score > 50) _unitType = ZCUnitTypeBarbarian;
	else _unitType = ZCUnitTypeKnightLance;
}

- (NSString*)address {
	return [NSString stringWithFormat:@"%p", self];
}

@end

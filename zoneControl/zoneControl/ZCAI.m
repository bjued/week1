//
//  ZCAI.m
//  zoneControl
//
//  Created by Brandon Jue on 6/17/14.
//  Copyright (c) 2014 bjued. All rights reserved.
//

#import "ZCAI.h"

@implementation ZCAI

+ (instancetype)teamId:(int)teamId color:(CCColor*)color symbolName:(NSString*)sym decisionTime:(int)time armySize:(int)size {
	return [[ZCAI alloc]initWithId:teamId color:color symbolName:sym decisionTime:time armySize:size];
}

- (id)initWithId:(int)teamId color:(CCColor*)color symbolName:(NSString*)sym decisionTime:(int)time armySize:(int)size {
    self = [super initWithId:teamId color:color symbolName:sym];
    if (!self) return nil;
	
	_decisionDelta = time;
	_nextDecision = time;
	_preferredArmySize = size;
	
	return self;
}

- (BOOL)decisionTime:(CCTime)elapsed {
	if (elapsed > _nextDecision) {
		_nextDecision += _decisionDelta;
		
		return (_units.count > _preferredArmySize + arc4random_uniform(31) - 15);
	}
	return NO;
}



@end

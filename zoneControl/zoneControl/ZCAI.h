//
//  ZCAI.h
//  zoneControl
//
//  Created by Brandon Jue on 6/17/14.
//  Copyright (c) 2014 bjued. All rights reserved.
//

#import "ZCTeam.h"

@interface ZCAI : ZCTeam {
	int _decisionDelta;
	int _nextDecision;
	int _preferredArmySize;
}

+ (instancetype)teamId:(int)teamId color:(CCColor*)color symbolName:(NSString*)sym decisionTime:(int)time armySize:(int)size;

- (BOOL)decisionTime:(CCTime)elapsed;

@end

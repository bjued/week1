//
//  ZCTeam.h
//  zoneControl
//
//  Created by Brandon Jue on 6/16/14.
//  Copyright (c) 2014 bjued. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "cocos2d.h"

@class ZCFlag, ZCBase;
@interface ZCTeam : NSObject {
	int _teamId;
	NSMutableDictionary *_bases;
	NSMutableDictionary *_units;
	ZCFlag *_flag;
	CCLabelTTF *_scoreLabel;
	int _score;
	NSString *_symbolName;
	CCColor *_color;
	ZCUnitType *_unitType;
}
@property (nonatomic, assign) int teamId;
@property (nonatomic, strong) NSMutableDictionary *bases;
@property (nonatomic, strong) NSMutableDictionary *units;
@property (nonatomic, strong) ZCFlag *flag;
@property (nonatomic, strong) CCLabelTTF *scoreLabel;
@property (nonatomic, strong) NSString *symbolName;
@property (nonatomic, strong) CCColor *color;
@property (nonatomic, assign) ZCUnitType *unitType;

+ (instancetype)teamId:(int)i color:(CCColor*)color symbolName:(NSString*)sym;
- (id)initWithId:(int)teamId color:(CCColor*)color symbolName:(NSString*)sym;

- (void)removeBase:(ZCBase*)base;
- (void)addKills:(int)kills;

- (NSString*)address;

@end

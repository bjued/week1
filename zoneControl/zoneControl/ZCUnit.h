//
//  ZCUnit.h
//  zoneControl
//
//  Created by Brandon Jue on 6/16/14.
//  Copyright 2014 bjued. All rights reserved.
//

#import "ZCCell.h"

@class ZCTeam;
@interface ZCUnit : ZCCell {
	NSArray *_attackPattern;
	NSArray *_movePattern;
	
	int _attack;
	
	CGPoint _gridPosition;
}
@property (nonatomic, strong) NSArray *attackPattern;
@property (nonatomic, strong) NSArray *movePattern;
@property (nonatomic, assign) CGPoint gridPosition;

+ (instancetype)unitTeam:(ZCTeam*)team;

- (int)attack;

@end

//
//  ZCCell.h
//  zoneControl
//
//  Created by Brandon Jue on 6/17/14.
//  Copyright 2014 bjued. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "cocos2d.h"

typedef enum {
	ZCCellTypeBase,
	ZCCellTypeUnit,
	ZCCellTypeFlag,
} ZCCellType;

@class ZCTeam;
@interface ZCCell : CCNode {
	ZCCellType _type;
    int _teamId;
	int _health;
	NSMutableArray *_colored;
}
@property (nonatomic, assign) ZCCellType type;
@property (nonatomic, assign) int teamId;
@property (nonatomic, assign) int health;

- (void)destroy;

- (NSString*)address;

@end

//
//  ZCGrid.h
//  zoneControl
//
//  Created by Brandon Jue on 6/16/14.
//  Copyright 2014 bjued. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "cocos2d.h"

#define ZCBuffer 50
#define ZCGridSize 500
#define ZCTileSize 10.0
#define ZCZoneSize 100.0

#define ZCFlagZ 10000
#define ZCUnitZ 1000
#define ZCBaseZ 10

@class ZCGrid, ZCBase, ZCUnit;
@protocol ZCGridDelegate <NSObject>
@optional
- (void)grid:(ZCGrid*)grid touchBeganAtStringPoint:(NSString*)point;
- (void)grid:(ZCGrid*)grid touchEndedAtStringPoint:(NSString*)point;

@end

@interface ZCGrid : CCNode {
	NSMutableDictionary *_cells;
}
@property (nonatomic, strong) NSMutableDictionary *cells;
@property (nonatomic, assign) id<ZCGridDelegate> delegate;

+ (instancetype)grid;

- (void)addBase:(ZCBase*)base;
- (void)removeBase:(ZCBase*)base;
- (void)addUnit:(ZCUnit*)unit;
- (void)removeUnit:(ZCUnit*)unit;

@end

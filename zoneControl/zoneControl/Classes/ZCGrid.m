//
//  ZCGrid.m
//  zoneControl
//
//  Created by Brandon Jue on 6/16/14.
//  Copyright 2014 bjued. All rights reserved.
//

#import "ZCGrid.h"
#import "ZCBase.h"
#import "ZCUnit.h"

@implementation ZCGrid
@synthesize cells = _cells;
@synthesize delegate;

+ (instancetype)grid {
	return [[ZCGrid alloc]init];
}

- (id)init {
    self = [super init];
    if (!self) return nil;
    
	glClearColor(0, 0, 0, 1);
	
	self.userInteractionEnabled = YES;
	self.contentSize = CGSizeMake(ZCBuffer*2 + ZCGridSize, ZCBuffer*2 + ZCGridSize);
	
	self.cells = [NSMutableDictionary dictionaryWithCapacity:ZCGridSize*ZCGridSize/ZCTileSize/ZCTileSize];
	
	for (int i=0; i<ZCGridSize/ZCTileSize; i++) {
		for (int j=0; j<ZCGridSize/ZCTileSize; j++) {
			CCColor *color = [CCColor colorWithWhite:0.9 alpha:0.5];
			CCNodeColor *tile = [CCNodeColor nodeWithColor:color width:ZCTileSize-2 height:ZCTileSize-2];
			
			double x = j * ZCTileSize + 1 + ZCBuffer;
			double y = i * ZCTileSize + 1 + ZCBuffer;
			tile.position = ccp(x, y);
			
			[self addChild:tile];
		}
	}
	for (int i=0; i<ZCGridSize/ZCZoneSize; i++) {
		for (int j=0; j<ZCGridSize/ZCZoneSize; j++) {
			CCColor *color;
			if ((i + j) % 2 == 0) {
				color = [CCColor colorWithWhite:0.2 alpha:0.5];
			}
			else {
				color = [CCColor colorWithWhite:0.3 alpha:0.5];
			}
			CCNodeColor *tile = [CCNodeColor nodeWithColor:color width:ZCZoneSize-2 height:ZCZoneSize-2];
			
			double x = j * ZCZoneSize + 1 + ZCBuffer;
			double y = i * ZCZoneSize + 1 + ZCBuffer;
			tile.position = ccp(x, y);
			
			[self addChild:tile];
		}
	}
	
	return self;
}

- (void)addBase:(ZCBase*)base {
	// find the four cells the base takes up and claim them
	CGPoint point = ccp(base.position.x - ZCTileSize/2, base.position.y - ZCTileSize/2);
	for (int i=0; i<2; i++) {
		for (int j=0; j<2; j++) {
			CGPoint cell = ccp(point.x + ZCTileSize * i, point.y + ZCTileSize * j);
			NSString *key = NSStringFromCGPoint(cell);
			if (_cells[key]) CCLOG(@"ERROR, %@ is already in %@, couldn't put %@", _cells[key], key, base);
			else _cells[key] = base;
		}
	}
	[self addChild:base z:ZCBaseZ];
}
- (void)removeBase:(ZCBase*)base {
	// find the four cells the base takes up and clean them
	CGPoint point = ccp(base.position.x - ZCTileSize/2, base.position.y - ZCTileSize/2);
	for (int i=0; i<2; i++) {
		for (int j=0; j<2; j++) {
			CGPoint cell = ccp(point.x + ZCTileSize * i, point.y + ZCTileSize * j);
			NSString *key = NSStringFromCGPoint(cell);
			[_cells removeObjectForKey:key];
		}
	}
	[base removeFromParent];
}
- (void)addUnit:(ZCUnit*)unit {
	// find the cell the unit takes up and claim them
	NSString *key = NSStringFromCGPoint(unit.gridPosition);
	if (_cells[key]) CCLOG(@"ERROR, %@ is already in %@, couldn't put %@", _cells[key], key, unit);
	else _cells[key] = unit;
	
	[self addChild:unit z:ZCUnitZ];
}
- (void)removeUnit:(ZCUnit*)unit {
	// find the cell the unit takes up and clean them
	NSString *key = NSStringFromCGPoint(unit.gridPosition);
	[_cells removeObjectForKey:key];
	
	[unit removeFromParent];
}

#pragma mark - Touch Handler
- (void)touchBegan:(UITouch *)touch withEvent:(UIEvent *)event {
    CGPoint touchLoc = [touch locationInNode:self];

//    CCLOG(@"TouchBegan @ %@",NSStringFromCGPoint(touchLoc));
	if (delegate && [delegate respondsToSelector:@selector(grid:touchBeganAtStringPoint:)]) {
		[delegate performSelector:@selector(grid:touchBeganAtStringPoint:) withObject:self withObject:NSStringFromCGPoint(touchLoc)];
	}
}
- (void)touchEnded:(UITouch *)touch withEvent:(UIEvent *)event {
    CGPoint touchLoc = [touch locationInNode:self];
	
//    CCLOG(@"TouchEnded @ %@",NSStringFromCGPoint(touchLoc));
	if (delegate && [delegate respondsToSelector:@selector(grid:touchEndedAtStringPoint:)]) {
		[delegate performSelector:@selector(grid:touchEndedAtStringPoint:) withObject:self withObject:NSStringFromCGPoint(touchLoc)];
	}
}

@end

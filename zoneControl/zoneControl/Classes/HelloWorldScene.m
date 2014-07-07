//
//  HelloWorldScene.m
//  zoneControl
//
//  Created by Brandon Jue on 6/16/14.
//  Copyright bjued 2014. All rights reserved.
//

#import "HelloWorldScene.h"
#import "IntroScene.h"
#import "ZCGrid.h"
#import "ZCBase.h"
#import "ZCUnit.h"
#import "ZCTeam.h"
#import "ZCAI.h"
#import "ZCFlag.h"

#define pointKey(x, y) NSStringFromCGPoint(CGPointMake(x, y))

#pragma mark - HelloWorldScene
@implementation HelloWorldScene {
    CCSprite *_sprite;
}

#pragma mark - Create & Destroy
+ (HelloWorldScene *)scene {
    return [[self alloc] init];
}

- (id)init {
    self = [super init];
    if (!self) return nil;
    
    self.userInteractionEnabled = YES;
    
	glClearColor(0.2, 0.2, 0.2, 1);
	
	_grid = [ZCGrid grid];
	_grid.delegate = self;
	_scrollView = [[CCScrollView alloc]initWithContentNode:_grid];
	_scrollView.flipYCoordinates = NO;
	[self addChild:_scrollView];
	
	_teamColors = [NSMutableArray arrayWithObjects:
				   [CCColor redColor],
				   [CCColor blueColor],
				   [CCColor greenColor],
				   [CCColor yellowColor], nil];
	_teamSymbols = [NSMutableArray arrayWithObjects:
					@"flag_circle.png",
					@"flag_star.png",
					@"flag_lines.png",
					@"flag_diamond.png", nil];
	CGFloat small = ZCZoneSize / 2.0 + ZCBuffer;
	CGFloat big = ZCGridSize - ZCZoneSize / 2.0 + ZCBuffer;
	_startLocations = [NSMutableArray arrayWithObjects:
					   NSStringFromCGPoint(ccp(small, small)),
					   NSStringFromCGPoint(ccp(big, big)),
					   NSStringFromCGPoint(ccp(small, big)),
					   NSStringFromCGPoint(ccp(big, small)), nil];
	_teams = [NSMutableArray array];
	ZCTeam *team = [ZCTeam teamId:0 color:_teamColors[0] symbolName:_teamSymbols[0]];
	ZCBase *base = [team.bases allValues][0];
	base.position = CGPointFromString(_startLocations[0]);
	[_grid addBase:base];
	[_teams addObject:team];
	team.scoreLabel.position = ccp([CCDirector sharedDirector].viewSize.width - team.scoreLabel.contentSize.width, [CCDirector sharedDirector].viewSize.height - team.scoreLabel.contentSize.height/2);
	[self addChild:team.scoreLabel];
	
	for (int i=1; i<4; i++) {
		ZCAI *ai = [ZCAI teamId:i color:_teamColors[i] symbolName:_teamSymbols[i] decisionTime:1 armySize:12];
		ZCBase *base = [ai.bases allValues][0];
		base.position = CGPointFromString(_startLocations[i]);
		[_grid addBase:base];
		[_teams addObject:ai];
		ai.scoreLabel.position = ccp([CCDirector sharedDirector].viewSize.width - ai.scoreLabel.contentSize.width, [CCDirector sharedDirector].viewSize.height - ai.scoreLabel.contentSize.height/2 - ai.scoreLabel.contentSize.height * i);
		[self addChild:ai.scoreLabel];
	}
	
	_tickDelta = 1;
	_elapsed = 0;
	_nextTick = _tickDelta;
	
//	NSMutableArray *array = [NSMutableArray arrayWithObjects:@(3), @(7), @(8), @(5), @(2), @(1), @(9), @(5), @(4), nil];
//	NSLog(@"%@", array);
//	[self quicksortArray:&array left:0 right:(int)array.count-1];
//	NSLog(@"%@", array);
	
	return self;
}

//- (void)quicksortArray:(NSMutableArray**)array left:(int)l right:(int)r {
//	if (l < r) {
//		int left = [self partitionArray:array left:l right:r];
//		[self quicksortArray:array left:l right:r - 1];
//		[self quicksortArray:array left:left + 1 right:r];
//	}
//}
//
//- (int)partitionArray:(NSMutableArray**)array left:(int)l right:(int)r {
//	int pivotIndex = [self choosePivotArray:*array left:l right:r];
//	int pivotValue = [(*array)[pivotIndex] intValue];
//	NSLog(@"%d,%d => %d: %d", l, r, pivotIndex, pivotValue);
//	int stored = l;
//	[self swapIndex:pivotIndex withIndex:r inArray:array];
//	for (int i=l; i<r; i++) {
//		if ([(*array)[i] intValue] <= pivotValue) {
//			[self swapIndex:i withIndex:stored inArray:array];
//			stored += 1;
//		}
//	}
//	[self swapIndex:stored withIndex:r inArray:array];
//	return stored;
//}
//
//- (int)choosePivotArray:(NSMutableArray*)array left:(int)l right:(int)r {
//	return r; // change to middle
//}
//- (void)swapIndex:(int)i withIndex:(int)j inArray:(NSMutableArray**)array {
//	if (i==j) return;
//	NSLog(@"Swapping %d:%@ with %d:%@", i, (*array)[i], j, (*array)[j]);
//	id holder = (*array)[i];
//	(*array)[i] = (*array)[j];
//	(*array)[j] = holder;
//}

- (void)dealloc {
    // clean up code goes here
}

#pragma mark - Enter & Exit
- (void)onEnter {
    // always call super onEnter first
    [super onEnter];
    
    // In pre-v3, touch enable and scheduleUpdate was called here
    // In v3, touch is enabled by setting userInteractionEnabled for the individual nodes
    // Per frame update is automatically enabled, if update is overridden
}
- (void)onExit {
    // always call super onExit last
    [super onExit];
}

#pragma mark - Button Callbacks
- (void)onBackClicked:(id)sender {
    // back to intro scene with transition
    [[CCDirector sharedDirector] replaceScene:[IntroScene scene] withTransition:[CCTransition transitionPushWithDirection:CCTransitionDirectionRight duration:1.0f]];
}

#pragma mark - Grid Delegate
- (void)grid:(ZCGrid *)grid touchBeganAtStringPoint:(NSString *)point {
	
}
- (void)grid:(ZCGrid *)grid touchEndedAtStringPoint:(NSString *)point {
	ZCTeam *myTeam = _teams[0];
	ZCFlag *myFlag = myTeam.flag;
	
	if (![myFlag parent]) [_grid addChild:myFlag z:ZCFlagZ];
	
	CGPoint pt = CGPointFromString(point);
	pt.x = ((int)(pt.x / ZCTileSize) * ZCTileSize) + ZCTileSize / 2; // snap to middle of a tile
	pt.y = ((int)(pt.y / ZCTileSize) * ZCTileSize) + ZCTileSize * 2; // snap to bottom of a tile
	myFlag.position = pt;
}

#pragma mark - Scheduled update
- (void)update:(CCTime)delta {
	_elapsed += delta;
	if (_elapsed >= _nextTick) { // we've added one full tickDelta
		_nextTick += _tickDelta;
		NSMutableArray *kills = [NSMutableArray array];
		for (ZCTeam *team in _teams) {
			if (team.bases.count > 0) {
				NSArray *teamKills = [self teamActions:team];
				[kills addObjectsFromArray:teamKills];
				[team addKills:(int)teamKills.count];
				
				// determine whether to add a new unit
				[self addUnitToTeam:team];
			}
		}
		
		// remove the dead
		for (ZCCell *cell in kills) {
			ZCTeam *team = _teams[cell.teamId];
			
			CCActionFadeOut *fade = [CCActionFadeOut actionWithDuration:_nextTick-_elapsed];
			CCActionCallBlock *callback = nil;
			switch (cell.type) {
				case ZCCellTypeBase: {
					callback = [CCActionCallBlock actionWithBlock:^{
						[_grid removeBase:(ZCBase*)cell];
					}];
					[team removeBase:(ZCBase*)cell];
				} break;
				case ZCCellTypeUnit: {
					callback = [CCActionCallBlock actionWithBlock:^{
						[_grid removeUnit:(ZCUnit*)cell];
					}];
					[team.units removeObjectForKey:cell.address];
				} break;
				default: break;
			}
			[cell runAction:[CCActionSequence actions:fade, callback, nil]];
		}
	}
	for (int i=1; i<_teams.count; i++) {
		ZCTeam *team = _teams[i];
		if ([team isKindOfClass:[ZCAI class]] && team.bases.count > 0) {
			ZCAI *ai = (ZCAI*)team;
			if ([ai decisionTime:_elapsed]) {
				int attackTeam = arc4random_uniform((int)_teams.count-1);
				if (attackTeam>=i) attackTeam += 1;
				
				ZCTeam *defendingTeam = _teams[attackTeam];
				ZCFlag *flag = ai.flag;
				BOOL shouldChange = NO;
				if (![flag parent]) {
					[_grid addChild:flag z:ZCFlagZ];
					shouldChange = YES;
				}
				else {
					shouldChange = arc4random_uniform(100) < 10; // 10% chance to change
				}
				
				if (shouldChange && defendingTeam.bases.count > 0) {
					CGPoint pt = ((ZCBase*)[defendingTeam.bases allValues][0]).position;
					pt.y = ((int)(pt.y / ZCTileSize) * ZCTileSize) + ZCTileSize * 2; // snap to bottom of a tile
					switch (ai.teamId) {
						case  0: { // bottom left
							pt.x -= ZCTileSize / 2;
							pt.y -= ZCTileSize;
						} break;
						case  1: { // top right
							pt.x += ZCTileSize / 2;
						} break;
						case  2: { // top left
							pt.x -= ZCTileSize / 2;
						} break;
						case  3: { // bottom right
							pt.x += ZCTileSize / 2;
							pt.y -= ZCTileSize;
						} break;
						default:
							break;
					}
					flag.position = pt;
				}
			}
		}
	}
}

- (int)distanceFrom:(CGPoint)start to:(CGPoint)finish {
	return sqrt(pow(abs(finish.x - start.x), 2) + pow(abs(finish.y - start.y), 2));
}
- (NSArray*)teamActions:(ZCTeam*)team {
	CGPoint end = team.flag.position;
	end.y += ZCTileSize / 2 - ZCTileSize * 2; // head to the bottom of the flag
	
	NSMutableArray *kills = [NSMutableArray array];
	for (ZCUnit *unit in [team.units allValues]) {
		BOOL shouldAttack = YES;
		BOOL shouldMove = team.flag.parent?YES:NO;
		BOOL attacked = NO;
		BOOL moved = NO;
		
		CGPoint start = unit.gridPosition;
		
		if (shouldAttack) {
			NSMutableArray *keys = [NSMutableArray array];
			for (NSValue *value in unit.attackPattern) {
				CGPoint point = [value CGPointValue];
				[keys addObject:pointKey(start.x + ZCTileSize * point.x, start.y + ZCTileSize * point.y)];
			}
			for (NSString *key in keys) {
				if (_grid.cells[key]) {
					ZCCell *cell = _grid.cells[key];
					
					if (cell.health > 0) {					// this guy is still alive
						if (cell.teamId != team.teamId) {	// this is an enemy
							cell.health -= [unit attack];
							double segment = (_nextTick-_elapsed)/4.0;
							[unit runAction:
							 [CCActionSequence actions:
							  [CCActionRotateTo actionWithDuration:segment angle:12],
							  [CCActionRotateTo actionWithDuration:segment*2 angle:-12],
							  [CCActionRotateTo actionWithDuration:segment angle:0], nil]];
							attacked = YES;
							if (cell.health <= 0) {
								[kills addObject:cell];
							}
							break;
						}
					}
				}
			}
		}
			
		shouldMove = shouldMove && !attacked;
		if (shouldMove) {
			NSString *bestKey = NSStringFromCGPoint(start);
			int bestDistance = [self distanceFrom:start to:end]; // defaults to just standing still
			// construct move array
			NSMutableArray *keys = [NSMutableArray array];
			for (NSValue *value in unit.movePattern) {
				CGPoint point = [value CGPointValue];
				CGFloat x = start.x + ZCTileSize * point.x;
				CGFloat y = start.y + ZCTileSize * point.y;
				if (x >= ZCBuffer && x <= ZCGridSize+ZCBuffer && y >= ZCBuffer && y <= ZCGridSize+ZCBuffer) {
					[keys addObject:pointKey(start.x + ZCTileSize * point.x, start.y + ZCTileSize * point.y)];
				}
			}
			for (NSString *key in keys) {
				if (!_grid.cells[key]) {
					int distance = [self distanceFrom:CGPointFromString(key) to:end];
					if (distance <= bestDistance) {
						bestKey = key;
						bestDistance = distance;
						moved = YES;
					}
				}
			}
			
			if (moved) {
				[_grid.cells removeObjectForKey:NSStringFromCGPoint(start)];
				CGPoint point = CGPointFromString(bestKey);
				[unit runAction:[CCActionMoveTo actionWithDuration:_nextTick-_elapsed  position:point]];
				unit.gridPosition = point;
				_grid.cells[bestKey] = unit;
			}
		}
	}
	
	return kills;
}

- (void)addUnitToTeam:(ZCTeam*)team {
	for (ZCBase *base in [team.bases allValues]) {
		// (di, dj) is a vector - direction in which we move right now
		int di = ZCTileSize;
		int dj = 0;
		// length of current segment
		int segment_length = 2;
		
		// current position (i, j) and how much of current segment we passed
		CGFloat i = base.position.x - ZCTileSize / 2;
		CGFloat j = base.position.y + ZCTileSize / 2 + ZCTileSize;
		int segment_passed = 0;
		for (int k = 0; k < pow(ZCZoneSize, 2) / pow(ZCTileSize, 2) - 4; k++) {
			// do whatever
			CGPoint point = ccp(i, j);
			NSString *key = NSStringFromCGPoint(point);
			if (!_grid.cells[key]) {
				ZCUnit *unit = [ZCUnit unitTeam:team];
				unit.position = point;
				unit.gridPosition = point;
				[_grid addUnit:unit];
				team.units[unit.address] = unit;
				break;
			}
			
			// make a step, add 'direction' vector (di, dj) to current position (i, j)
			i += di;
			j += dj;
			segment_passed++;
			
			if (segment_passed == segment_length) {
				// done with current segment
				segment_passed = 0;
				
				// 'rotate' directions
				int buffer = -di;
				di = dj;
				dj = buffer;
				
				// increase segment length if necessary
				if (di == 0) {
					segment_length++;
				}
			}
		}
	}
}

@end

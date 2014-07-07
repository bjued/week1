//
//  ZCFlag.h
//  zoneControl
//
//  Created by Brandon Jue on 6/17/14.
//  Copyright 2014 bjued. All rights reserved.
//

#import "ZCCell.h"

@class ZCTeam;
@interface ZCFlag : ZCCell {
}

+ (instancetype)flagTeam:(ZCTeam*)team;

@end

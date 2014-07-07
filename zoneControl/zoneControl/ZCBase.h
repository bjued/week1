//
//  ZCBase.h
//  zoneControl
//
//  Created by Brandon Jue on 6/16/14.
//  Copyright 2014 bjued. All rights reserved.
//

#import "ZCCell.h"

@class ZCTeam;
@interface ZCBase : ZCCell {
}

+ (instancetype)baseTeam:(ZCTeam*)team;

@end

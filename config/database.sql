-- --------------------------------------------------------

--
-- Table `tl_iso_attributes`
--

CREATE TABLE `tl_iso_attributes` (
  `mcwvariant_columnFields` text NULL,
  `mcwvariant_mandatory` char(1) NOT NULL default '',
  `mcwvariant_inputType` varchar(64) NOT NULL default '',
  `mcwvariant_autochooseFirstOption` char(1) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
package tool

import "os"

func GetMode() string {
	env := os.Getenv("RUN_MODE")
	if env == "" {
		env = "dev"
	}
	return env
}

func GetBuild() string {
	dockerENV := os.Getenv("BUILD_ENV")
	if dockerENV == "" {
		dockerENV = "Linux"
	}
	return dockerENV
}

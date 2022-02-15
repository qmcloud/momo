package logic

import (
	"backend/common/errorx"
	"backend/service/k8s/cmd/api/internal/svc"
	"backend/service/k8s/cmd/api/internal/types"
	"context"
	"github.com/zeromicro/go-zero/core/logx"
	"log"

	metav1 "k8s.io/apimachinery/pkg/apis/meta/v1"
	"k8s.io/client-go/kubernetes"
	"k8s.io/client-go/tools/clientcmd"
)

type DeleteDeploymentLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewDeleteDeploymentLogic(ctx context.Context, svcCtx *svc.ServiceContext) DeleteDeploymentLogic {
	return DeleteDeploymentLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *DeleteDeploymentLogic) DeleteDeployment(req types.Request) (*types.Response, error) {
	configPath := "k8s/kube.config"

	config, err := clientcmd.BuildConfigFromFlags("", configPath)
	if err != nil {
		log.Fatal(err)
	}
	clientSet, err := kubernetes.NewForConfig(config)
	if err != nil {
		log.Fatal(err)
	}
	//检查创建人，创建人才可以删除自己的数据
	//系统数据不能删除
	err = clientSet.AppsV1().Deployments(req.NameSpace).Delete(context.TODO(), req.Name, metav1.DeleteOptions{})
	return nil, errorx.NewCodeError(200, "成功", err)
}
